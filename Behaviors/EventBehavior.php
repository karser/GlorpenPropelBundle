<?php
class EventBehavior extends Behavior {
	
	protected $tableModificationOrder = 51;
	
	protected $parameters = array();
	
	public function postInsert(){
		return <<<EOF
EventDispatcherProxy::trigger('model.insert.post', new ModelEvent(\$this));
EOF;
	}
	public function postUpdate(){
		return <<<EOF
EventDispatcherProxy::trigger(array('update.post', 'model.update.post'), new ModelEvent(\$this));
EOF;
	}
	public function postDelete(){
		return <<<EOF
EventDispatcherProxy::trigger(array('delete.post', 'model.delete.post'), new ModelEvent(\$this));
EOF;
	}
	public function postSave(){
		return <<<EOF
EventDispatcherProxy::trigger('model.save.post', new ModelEvent(\$this));
EOF;
	}
	
	public function preInsert(){
		return <<<EOF
EventDispatcherProxy::trigger('model.insert.pre', new ModelEvent(\$this));
EOF;
	}
	public function preUpdate(){
		return <<<EOF
EventDispatcherProxy::trigger(array('update.pre', 'model.update.pre'), new ModelEvent(\$this));
EOF;
	}
	public function preSave(){
		return <<<EOF
EventDispatcherProxy::trigger('model.save.pre', new ModelEvent(\$this));
EOF;
	}
	
	public function preDeleteQuery(){
		return <<<EOF
// placeholder, issue #5
EOF;
	}
	
	public function postDeleteQuery(){
		return <<<EOF
EventDispatcherProxy::trigger(array('delete.post','query.delete.post'), new QueryEvent(\$this));
EOF;
	}

	public function preSelectQuery(){
		return <<<EOF
EventDispatcherProxy::trigger('query.select.pre', new QueryEvent(\$this));
EOF;
	}
	
	public function preUpdateQuery(){
		return <<<EOF
EventDispatcherProxy::trigger(array('update.pre', 'query.update.pre'), new QueryEvent(\$this));
EOF;
	}

	public function postUpdateQuery(){
		return <<<EOF
EventDispatcherProxy::trigger(array('update.post', 'query.update.post'), new QueryEvent(\$this));
EOF;
	}
	
	
	public function objectFilter(&$script){
		$rep = <<<EOF
	EventDispatcherProxy::trigger(array('construct','model.construct'), new ModelEvent(\$this));
	
EOF;
		$script = preg_replace('/(parent::__construct[^}]*)/', '\1'.$rep, $script, 1, $count);
		if($count == 0){
			$construct = <<<EOF
public function __construct(){
		parent::__construct();
	{$rep}}

	
EOF;
			$script = preg_replace('#(public function .*?}.*?)(/\*\*)#s', '\1'.$construct.'\2', $script, 1);
		}
		
		// fix for SoftDelete - handle preDelete as script filter
		$script = preg_replace('/(([\t ]+)public function delete.*?try[^{]{)/s', '\1'."\n".'\2\2\2EventDispatcherProxy::trigger(array(\'delete.pre\',\'model.delete.pre\'), new ModelEvent(\$this));', $script, 1);
		
	}
	
	public function queryFilter(&$script)
	{
		$rep = <<<EOF
	EventDispatcherProxy::trigger(array('construct','query.construct'), new QueryEvent(\$this));
	
EOF;
		$script = preg_replace('/(parent::__construct[^}]*)/', '\1'.$rep, $script);
		
		// fix for SoftDelete - handle preDelete as script filter
		$script = preg_replace('/(([\t ]+)protected function basePreDelete.*?{)/s', '\1'."\n".'\2\2EventDispatcherProxy::trigger(array(\'delete.pre\',\'query.delete.pre\'), new QueryEvent(\$this));', $script, 1);
	}
	
	public function peerFilter(&$script){
		$peerClass = $this->getTable()->getNamespace().'\\om\\Base'.$this->getTable()->getPhpName().'Peer';
		
		$script .= <<<EOF
EventDispatcherProxy::trigger(array('construct','peer.construct'), new PeerEvent('{$peerClass}'));

EOF;
	}
	
	public function objectMethods($builder)
	{
		$builder->declareClass('Glorpen\\Propel\\PropelBundle\\Events\\ModelEvent');
		$builder->declareClass('Glorpen\\Propel\\PropelBundle\\Dispatcher\\EventDispatcherProxy');
		
		$events=array('save','delete','update','insert');
		$types=array('commit','rollback');
		
		$ret='';
		
		foreach($types as $t){
			$ut = ucfirst($t);
			$ret.="public function pre{$ut}(\\PropelPDO \$con = null){}\n";
			foreach($events as $e){
				$ue = ucfirst($e);
				$ret.="public function pre{$ut}{$ue}(\\PropelPDO \$con = null){}\n";
			}
		}
		
		return $ret;
	}
	
	public function queryMethods($builder)
	{
		$builder->declareClass('Glorpen\\Propel\\PropelBundle\\Events\\QueryEvent');
		$builder->declareClass('Glorpen\\Propel\\PropelBundle\\Dispatcher\\EventDispatcherProxy');
	}
	
	public function staticMethods($builder){
		$builder->declareClass('Glorpen\\Propel\\PropelBundle\\Events\\PeerEvent');
		$builder->declareClass('Glorpen\\Propel\\PropelBundle\\Dispatcher\\EventDispatcherProxy');
	}
	
}
