<?php
namespace App\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Utility\CakeTime;

class CommonBehavior extends Behavior
{
	
	public function initialize(array $config)
	{
	    // Some initialization code here
	}

	/*
	* Make slug
	*/
	public function makeSlug ($string, $id=null, $fieldname='slug',$translate=false) {
		$slug = $string;
				
		//remove non unicode character from string
		$regx = '/([\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})|./s';
		$slug  = preg_replace( $regx , '$1' , $slug );
		
		
		//remove unicode BOM character from string
		$regx = '\xef\xbb\xbf';
		$slug  = str_replace( $regx , '' , $slug );
		
		$slug = mb_strtolower($slug,'UTF-8');
		
		if($translate && extension_loaded('iconv')){
			//translate non ascii character to ascii character
			$slug = iconv( 'UTF-8' , 'US-ASCII//TRANSLIT//IGNORE' , $slug );
		}
		
		//remove special character
		if($translate && @preg_match( '//u', '' )){
			$slug = preg_replace( '/[^\\p{L}\\p{Nd}\-_]+/u' , '-' , $slug );
		} else {
			$slug = preg_replace( '/[\(\)\>\<\+\?\&\"\'\/\\\:\s\-\#\%\=\@\^\$\,\.\~\`\'\"\*\!]+/' , '-' , $slug );
		}
		
		$slug=trim($slug,"_-");
		
		$params = array ();
		$params ['conditions']= array();
		$params ['conditions'][$this->alias.'.'.$fieldname]= $slug;
		if (!is_null($id)) {
			$params ['conditions']['not'] = array($this->alias.'.id'=>$id);
		}
		$i = 0;		
		//check and make unique slug
		while (count($this->find ('all',$params))) {
			if (!preg_match ('/-{1}[0-9]+$/', $slug )) {
				$slug .= '-' . ++$i;
			} else {
				$slug = preg_replace ('/[0-9]+$/', ++$i, $slug );
			}
			$params ['conditions'][$this->alias.'.'.$fieldname]= $slug;
		}
		return $slug;
	}

	/* this function will unbind each model except those are given as inputs*/
	public function unbindModelAll($params=array()) {
        foreach(array(
                'hasOne' => array_keys($this->hasOne),
                'hasMany' => array_keys($this->hasMany),
                'belongsTo' => array_keys($this->belongsTo),
                'hasAndBelongsToMany' => array_keys($this->hasAndBelongsToMany)
        ) as $relation => $model) {
        		$model=array_diff($model, $params);
        		$this->unbindModel(array($relation => $model));
        }
    }

    //get current date time
	public function getCurrentDateTime(){
		return CakeTime::format('Y-m-d H:i:s',CakeTime::convert(time(),CakeTime::timezone()));
	}

	/*
	* Genrate random text
    */
    public function randText($length=40){
        $random= "";
        srand((double)microtime()*1000000);
        $strset  = "ABCDEFGHIJKLMNPQRSTUVWXYZ";
        $strset.= "abcdefghijklmnpqrstuvwxyz";
        $strset.= "123456789";
        // Add the special characters to $strset if needed
        
        for($i = 0; $i < $length; $i++) {
            $random.= substr($strset,(rand()%(strlen($strset))), 1);
        }
        return $random;
    }

}