<?php
require_once 'Collection.php';
require_once 'Type.php';
require_once 'User.php';
require_once 'File.php';
require_once 'Metatext.php';
require_once 'Tag.php';
require_once 'ItemsTags.php';

/**
 * @package Sitebuilder
 * @author Kris Kelly
 **/
class Item extends Kea_Record
{	
	
	public function setUp() {
		$this->hasOne("Collection","Item.collection_id");
		$this->hasOne("Type","Item.type_id");
		$this->hasOne("User","Item.user_id");
		$this->ownsMany("File","File.item_id");
		$this->ownsMany("Metatext", "Metatext.item_id");
		$this->hasMany("Tag as Tags", "ItemsTags.tag_id");
	}
	
	public function setTableDefinition() {
		$this->setTableName('items');
		
		$this->hasColumn("title","string",300, "notblank");
		$this->hasColumn("publisher","string",300);
		$this->hasColumn("language","string",null);
		$this->hasColumn("relation","string",null);
		$this->hasColumn("coverage","string",null);
		$this->hasColumn("rights","string",null);
		$this->hasColumn("description","string");
		$this->hasColumn("source","string",null);
		$this->hasColumn("subject","string",300);
		$this->hasColumn("creator","string",300);
		$this->hasColumn("additional_creator","string",300);
		$this->hasColumn("date","date");
		$this->hasColumn("added","timestamp");
		$this->hasColumn("modified","timestamp");
		
		$this->hasColumn("type_id","integer");
		$this->hasColumn("collection_id","integer");
		$this->hasColumn("user_id","integer");

	}
	
	
	///// TAGGING METHODS /////
	
	public function tagString($wrap = null, $delimiter = ',') {
		$string = '';
		foreach( $this->Tags as $key => $tag )
		{
			if($tag->exists()) {
				$name = $tag->__toString();
				$string .= (!empty($wrap) ? preg_replace("/$name/", $wrap, $name) : $name);
				$string .= ( ($key+1) < $this->Tags->count() ) ? $delimiter.' ' : '';
			}
		}
		
		return $string;
	}
	
	/**
	 * What should this function return? (if anything)
	 *
	 * @return void
	 * @author Kris Kelly
	 **/
	public function addTagString($string, $delimiter = ',') {
		$tagsArray = explode($delimiter, $string);
		foreach( $tagsArray as $key => $tagName )
		{
			$tagName = trim($tagName);
			$tag = new Tag();
			$tag = $tag->getTable()->findOrNew($tagName);
				$it = new ItemsTags();
				$it->Tag = $tag;
				
			//  Make a fake User for testing purposes	
				$user = new User();
				$user = $user->getTable()->find(1);
				if(!$user) $user = new User();
				$user->name = "FooUser";
				$it->User = $user;

				$this->ItemsTags[] = $it;
		}
	}
		
	/** If the $tag were a string and the keys of Tags were just the names of the tags, this would be:
	 * in_array(array_keys($this->Tags))
	 *
	 * @return boolean
	 **/
	public function hasTag($tag) {
		foreach( $this->Tags as $key => $oldTag )
		{
			if($tag instanceof Tag) {
				if($tag->name == $oldTag->name) {
					return true;
				}
			} else {
				if($tag == $oldTag->name) {
					return true;
				}
			}
		}
		return false;
	}
	
	///// END TAGGING METHODS /////
		
} // END class Item extends Kea_Domain_Record

?>