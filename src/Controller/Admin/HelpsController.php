<?php
namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * Helps Controller
 *
 * @property \App\Model\Table\HelpsTable $Helps
 */
class HelpsController extends AppController
{

    public function index($parent_id = null) {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        $this->set('title_for_layout',__('Helps List'));
        // find the main tile
        $this->set('parentsOptions', $this->Helps->parentsOptions());
        if ($parent_id) {
            $conditions = array('Helps.parent_id' => $parent_id, 'Helps.type' => 'help');
        } else {
            $conditions = array('Helps.parent_id IS NOT NULL', 'Helps.type' => 'help');
        }
        $options = array(
            'conditions' => $conditions,
            'order' => array(
                'Helps.lft'=>' DESC',
                'Helps.rght'=>' ASC',
            )
        );
        try {
            $this->set('helps', $this->Helps->find('all', $options)->toArray());
        } catch (NotFoundException $e) { 
            // when pagination error found redirect to first page e.g. paging page not found
            return $this->redirect(array('controller' => 'helps', 'action' => 'index', 'admin' => true));
        }
    }

    public function insert($help_id = null) {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        if(empty($help_id)){
            $this->set('title_for_layout',__('New Help'));
        } else {
            $this->set('title_for_layout',__('Edit Help'));
        }

        $this->set('parentsOptions', $this->Helps->parentsOptions());
        
        if ($this->request->is(array('post','put'))) {
            if(empty($this->request->data['Help']['slug'])) {
                $this->request->data['Help']['slug']=$this->request->data['Help']['title'];
            }
            
            $this->request->data['Help']['slug'] = $this->Helps->makeSlug($this->request->data['Help']['slug'], $this->request->data['Help']['id']);
    
            $this->request->data['Help']['user_id'] = $this->Auth->user('id');
            if (!empty($this->request->data['Help']['url'])) {
                $youtube = explode('?', $this->request->data['Help']['url']);
                $youtube = explode('=', $youtube[1]);
                $youtube = explode('&', $youtube[1]);   
                $this->request->data['Help']['url_src'] = $youtube[0];
            }
            
            if ($this->Helps->saveAll($this->request->data)) {
                $this->Session->setFlash(__('Help saved successfully'), 'notification_form', array(), 'notification');
                if(isset($this->params['url']['redirect_url'])){            
                    return $this->redirect(urldecode($this->params['url']['redirect_url']));
                } else {
                    return $this->redirect(array('controller' => 'helps', 'action' => 'index', 'admin' => true));
                }
            } else {
                $this->Session->setFlash(__('Help saved failed'), 'error_form', array(), 'error');
            }
        } elseif(!empty($help_id)) {
            $conditions = array(
                'Helps.id' => $help_id,
                'Helps.status'=> 1,
                'Helps.type'=> 'help'
            );
            
            if ($this->Helps->hasAny($conditions)){              
                $options = array(
                    'conditions'=>$conditions
                );
                $this->request->data=$this->Helps->find('first',$options);
            } else {
                $this->Session->setFlash(__('Help not found'), 'error_form', array(), 'error');
                $this->redirect($this->referer());
            }

        }

    }

    public function titles() {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        $this->set('title_for_layout',__('Main Title List'));
        
        $options = array(
            'conditions' => array(
                'Helps.parent_id' => null,
                'Helps.type' => 'help'
            ),
            'order' => array(
                'Helps.lft'=>' DESC',
                'Helps.rght'=>' ASC',
            )
        );
        
        try {
            $this->set('helps', $this->Helps->find('all', $options));
        } catch (NotFoundException $e) { 
            // when pagination error found redirect to first page e.g. paging page not found
            return $this->redirect(array('controller' => 'helps', 'action' => 'index', 'admin' => true));
        }
    }

    public function add($help_id = null) {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        if(empty($help_id)){
            $this->set('title_for_layout',__('New Main Title'));
        } else {
            $this->set('title_for_layout',__('Edit Main Title'));
        }
        
        if ($this->request->is(array('post','put'))) {

            $this->request->data['Help']['user_id'] = $this->Auth->user('id');

            if ($this->Helps->saveAll($this->request->data)) {
                $this->Session->setFlash(__('Title saved successfully'), 'notification_form', array(), 'notification');
                if(isset($this->params['url']['redirect_url'])){            
                    return $this->redirect(urldecode($this->params['url']['redirect_url']));
                } else {
                    return $this->redirect(array('controller' => 'helps', 'action' => 'titles', 'admin' => true));
                }
            } else {
                $this->Session->setFlash(__('Title saved failed'), 'error_form', array(), 'error');
            }
        } elseif(!empty($help_id)) {
            $conditions = array(
                'Helps.id' => $help_id,
                'Helps.parent_id' => null
            );
            
            if ($this->Helps->hasAny($conditions)){              
                $options = array(
                    'conditions'=>$conditions
                );
                $this->request->data=$this->Helps->find('first',$options);
            } else {
                $this->Session->setFlash(__('Title not found'), 'error_form', array(), 'error');
                $this->redirect($this->referer());
            }

        }

    }

    public function create($help_id = null) {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        if(empty($help_id)){
            $this->set('title_for_layout', __('New Site Video'));
        } else {
            $this->set('title_for_layout', __('Edit Site Video'));
        }

        $this->set('siteOptions', $this->Helps->siteOptions);
        
        if ($this->request->is(array('post','put'))) {
            if(empty($this->request->data['Help']['slug'])) {
                $this->request->data['Help']['slug']=$this->request->data['Help']['title'];
            }
            
            $this->request->data['Help']['slug'] = $this->Helps->makeSlug($this->request->data['Help']['slug'], $this->request->data['Help']['id']);
    
            $this->request->data['Help']['user_id'] = $this->Auth->user('id');
            if (!empty($this->request->data['Help']['url'])) {
                $youtube = explode('?', $this->request->data['Help']['url']);
                $youtube = explode('=', $youtube[1]);
                $youtube = explode('&', $youtube[1]);   
                $this->request->data['Help']['url_src'] = $youtube[0];
            }

            if (empty($this->request->data['Help']['id']) && !empty($this->request->data['Help']['photo'])) {
                $newpath = WWW_ROOT . 'uploads' . DS . 'videos';
                if (!file_exists($newpath)) {
                    mkdir($newpath, 0777, true);
                }
                copy(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $this->request->data['Help']['photo'], $newpath . DS . $this->request->data['Help']['photo']);
                copy(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . 't_' . $this->request->data['Help']['photo'], $newpath . DS . 't_' . $this->request->data['Help']['photo']);

                unlink(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $this->request->data['Help']['photo']);
                unlink(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . 't_' . $this->request->data['Help']['photo']);
            }
            
            if ($this->Helps->saveAll($this->request->data)) {
                $this->Session->setFlash(__('Site videos saved successfully'), 'notification_form', array(), 'notification');
                if(isset($this->params['url']['redirect_url'])){            
                    return $this->redirect(urldecode($this->params['url']['redirect_url']));
                } else {
                    return $this->redirect(array('controller' => 'helps', 'action' => 'videos', 'admin' => true));
                }
            } else {
                $this->Session->setFlash(__('Site videos saved failed'), 'error_form', array(), 'error');
            }
        } elseif(!empty($help_id)) {
            $conditions = array(
                'Helps.id' => $help_id,
                'Helps.status' => 1,
                'Helps.type !=' => 'help'
            );
            
            if ($this->Helps->hasAny($conditions)){              
                $options = array(
                    'conditions'=>$conditions
                );
                $this->request->data=$this->Helps->find('first',$options);
            } else {
                $this->Session->setFlash(__('Site videos not found'), 'error_form', array(), 'error');
                $this->redirect($this->referer());
            }

        }
        $lang_strings['upload_button'] = __('Upload a Picture');
        $this->set(compact('lang_strings'));
    }

    public function videos() {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        $this->set('title_for_layout',__('Site Videos List'));
        // find the siteOptions
        $this->set('siteOptions', $this->Helps->siteOptions);
        
        $conditions = array('Helps.parent_id = ' => null, 'Helps.type !=' => 'help');
        
        $options = array(
            'conditions' => $conditions,
            'order' => array(
                'Helps.lft'=>' DESC',
                'Helps.rght'=>' ASC',
            )
        );
        
        try {
            $this->set('helps', $this->Helps->find('all', $options));
        } catch (NotFoundException $e) { 
            // when pagination error found redirect to first page e.g. paging page not found
            return $this->redirect(array('controller' => 'helps', 'action' => 'videos', 'admin' => true));
        }
    }

    /**
    * method of help soft delete from admin
    */
    public function delete($help_id) {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        $this->autoRender=false;
        
        $conditions = array(
            'Helps.id' => $help_id,
            'Helps.status'=> 0,
        );
        
        if ($this->Helps->hasAny($conditions)){
            $this->Helps->delete($help_id);
        } else {
            $this->Session->setFlash(__('Can not delete'), 'error_form', array(), 'error');
        }       
            
        if(isset($this->params['url']['redirect_url'])){            
            return $this->redirect(urldecode($this->params['url']['redirect_url']));
        } else {
            return $this->redirect(array('controller' => 'helps', 'action' => 'index', 'admin' => true));
        }
        
    }
    
    /**
    * method of help active/deactive from admin
    */
    public function active($help_id,$active=NULL) {
        if ($this->Auth->user('account_level') != 51)
            throw new ForbiddenException;
        $this->autoRender=false;
        
        $conditions = array(
            'Helps.id' => $help_id,
        );
        
        if ($this->Helps->hasAny($conditions)){
            $this->Helps->updateAll(
                array(
                    'Helps.status' =>$active
                ),
                $conditions
            );
            $this->Helps->afterSave(false);
        } else {
            $this->Session->setFlash(__('Can not save'), 'error_form', array(), 'error');
        }           
        
        if(isset($this->params['url']['redirect_url'])){            
            return $this->redirect(urldecode($this->params['url']['redirect_url']));
        } else {
            return $this->redirect(array('controller' => 'helps', 'action' => 'index', 'admin' => true));
        }
        
    }


    function  moveup($help_id) {      
        $this->autoRender=false;
        
        $conditions = array(
            'Helps.id' => $help_id
        );
        
        if ($this->Helps->hasAny($conditions)){
            $options = array(
                'conditions'=>$conditions,
                'contain'=>array()
            );
            $help=$this->Helps->find('first',$options);
            if($help){
                $this->Helps->id=$help['Help']['id'];
                if($this->Helps->moveDown()==false)
                    $this->Session->setFlash(__('Sort failed'), 'error_form', array(), 'error');
            }   
        } else {
            $this->Session->setFlash(__('Sort failed'), 'error_form', array(), 'error');
        }           
            
        if(isset($this->params['url']['redirect_url'])){            
            return $this->redirect(urldecode($this->params['url']['redirect_url']));
        } else {
            return $this->redirect(array('action' => 'index'));
        }   
        
    }
    
    function movedown($help_id) {
        $conditions = array(
            'Helps.id' => $help_id
        );
        
        if ($this->Helps->hasAny($conditions)){
            $options = array(
                'conditions'=>$conditions,
                'contain'=>array()
            );
            $help=$this->Helps->find('first',$options);
            if($help){
                $this->Helps->id=$help['Help']['id'];
                if($this->Helps->moveUp()==false)
                    $this->Session->setFlash(__('Sort failed'), 'error_form', array(), 'error');
            }   
        } else {
            $this->Session->setFlash(__('Sort failed'), 'error_form', array(), 'error');
        }       
            
        if(isset($this->params['url']['redirect_url'])){            
            return $this->redirect(urldecode($this->params['url']['redirect_url']));
        } else {
            return $this->redirect(array('action' => 'index'));
        }
    }


}
