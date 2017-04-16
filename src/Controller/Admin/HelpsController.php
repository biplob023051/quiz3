<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Network\Exception\NotFoundException;

/**
 * Helps Controller
 *
 * @property \App\Model\Table\HelpsTable $Helps
 */
class HelpsController extends AppController
{

    public function index($parent_id = null) {
        $this->isAdminUser();
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

    public function insert($id = null) {
        $this->isAdminUser();
        if(empty($id)){
            $help = $this->Helps->newEntity();
            $this->set('title_for_layout',__('New Help'));
        } else {
            $help = $this->Helps->get($id, ['contain' => []]);
            $this->set('title_for_layout',__('Edit Help'));
        }
        
        if ($this->request->is(array('post','put'))) {
            if(empty($id)){
                $this->request->data['slug'] = $this->Helps->makeSlug($this->request->data['title'], 'Helps');
                $this->request->data['user_id'] = $this->Auth->user('id');
            }
            
            if (!filter_var($this->request->data['url'], FILTER_VALIDATE_URL) === false) {
                $youtube = explode('?', $this->request->data['url']);
                $youtube = explode('=', $youtube[1]);
                $youtube = explode('&', $youtube[1]);   
                $this->request->data['url_src'] = $youtube[0];
            }
// pr($this->request->data);
// exit;
            $help = $this->Helps->patchEntity($help, $this->request->data);
            // pr($help);
            // exit;
            if ($this->Helps->save($help)) {
                $this->Flash->success(__('Help saved successfully'));
                if(isset($this->request->query['redirect_url'])){            
                    return $this->redirect(urldecode($this->request->query['redirect_url']));
                } else {
                    return $this->redirect(array('controller' => 'helps', 'action' => 'index'));
                }
            } else {
                $this->Flash->error(__('Help saved failed'));
            }
        } 
        $this->set('parentsOptions', $this->Helps->parentsOptions());
        $this->set(compact('help'));

    }

    public function titles() {
        $this->isAdminUser();
        $this->set('title_for_layout',__('Main Title List'));
        $options = array(
            'conditions' => array(
                'Helps.parent_id IS NULL',
                'Helps.type' => 'help'
            ),
            'order' => array(
                'Helps.lft'=>' DESC',
                'Helps.rght'=>' ASC',
            )
        );
        
        try {
            $this->set('helps', $this->Helps->find('all', $options)->toArray());
        } catch (NotFoundException $e) { 
            // when pagination error found redirect to first page e.g. paging page not found
            return $this->redirect(array('controller' => 'helps', 'action' => 'index'));
        }
    }

    public function add($id = null) {
        $this->isAdminUser();
        if(empty($id)){
            $help = $this->Helps->newEntity();
            $this->set('title_for_layout',__('New Main Title'));
        } else {
            $help= $this->Helps->get($id, [
                'contain' => []
            ]);
            $this->set('title_for_layout',__('Edit Main Title'));
        }
        
        if ($this->request->is(array('post','put'))) {
            if(empty($id)){
                $this->request->data['user_id'] = $this->Auth->user('id');
            }
            $help = $this->Helps->patchEntity($help, $this->request->data, ['validate' => 'MainTitle']);
            if ($this->Helps->save($help)) {
                $this->Flash->success(__('Title saved successfully'));
                if(isset($this->request->query['redirect_url'])){            
                    return $this->redirect(urldecode($this->request->query['redirect_url']));
                } else {
                    return $this->redirect(array('controller' => 'helps', 'action' => 'titles'));
                }
            } else {
                $this->Flash->error(__('Title saved failed'));
            }
        }
        $this->set(compact('help'));

    }

    public function create($id = null) {
        $this->isAdminUser();
        if(empty($id)){
            $video = $this->Helps->newEntity();
            $this->set('title_for_layout', __('New Site Video'));
        } else {
            $video = $this->Helps->get($id, ['contain' => []]);
            $this->set('title_for_layout', __('Edit Site Video'));
        }

        $this->set('siteOptions', $this->siteOptions());
        
        if ($this->request->is(array('post','put'))) {
            if(empty($id)){
                $this->request->data['slug'] = $this->Helps->makeSlug($this->request->data['title'], 'Helps');
                $this->request->data['user_id'] = $this->Auth->user('id');
            }
            if (!filter_var($this->request->data['url'], FILTER_VALIDATE_URL) === false) {
                $youtube = explode('?', $this->request->data['url']);
                $youtube = explode('=', $youtube[1]);
                $youtube = explode('&', $youtube[1]);   
                $this->request->data['url_src'] = $youtube[0];
            }

            // if (empty($this->request->data['Help']['id']) && !empty($this->request->data['Help']['photo'])) {
            //     $newpath = WWW_ROOT . 'uploads' . DS . 'videos';
            //     if (!file_exists($newpath)) {
            //         mkdir($newpath, 0777, true);
            //     }
            //     copy(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $this->request->data['Help']['photo'], $newpath . DS . $this->request->data['Help']['photo']);
            //     copy(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . 't_' . $this->request->data['Help']['photo'], $newpath . DS . 't_' . $this->request->data['Help']['photo']);

            //     unlink(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . $this->request->data['Help']['photo']);
            //     unlink(WWW_ROOT . 'uploads' . DS . 'tmp' . DS . 't_' . $this->request->data['Help']['photo']);
            // }

            $video = $this->Helps->patchEntity($video, $this->request->data, ['validate' => 'VideoSection']);
            
            if ($this->Helps->save($video)) {
                $this->Flash->success(__('Site videos saved successfully'));
                if(isset($this->request->query['redirect_url'])){            
                    return $this->redirect(urldecode($this->request->query['redirect_url']));
                } else {
                    return $this->redirect(array('controller' => 'helps', 'action' => 'videos'));
                }
            } else {
                $this->Flash->error(__('Site videos saved failed'));
            }
        } 
        $lang_strings['upload_button'] = __('Upload a Picture');
        $this->set(compact('video', 'lang_strings'));
    }

    public function videos() {
        $this->isAdminUser();
        $this->set('title_for_layout',__('Site Videos List'));
        // find the siteOptions
        $this->set('siteOptions', $this->siteOptions());
        
        $conditions = array('Helps.parent_id IS NULL', 'Helps.type !=' => 'help');
        
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
            return $this->redirect(array('controller' => 'helps', 'action' => 'videos'));
        }
    }

    /**
    * method of help soft delete from admin
    */
    public function delete($id) {
        $this->isAdminUser();
        $this->autoRender=false;
        $help = $this->Helps->get($id, ['contain' => []]);
        if ($help){
            $this->Helps->delete($help);
            $this->Flash->success(__('You have successfully deleted!'));
        } else {
            $this->Flash->error(__('Not delete'));
        }       
        return $this->redirect($this->referer());
    }
    
    /**
    * method of help active/deactive from admin
    */
    public function active($id, $active=NULL) {
        $this->isAdminUser();
        $this->autoRender=false;
        $help = $this->Helps->get($id, ['contain' => []]);
        if ($help){
            $help->status = $active;
            $this->Helps->save($help);
            $message = empty($active) ? __('You have successfully deactivated!') : __('You have successfully activated');
            $this->Flash->success($message);
        } else {
            $this->Flash->error(__('Can not save'));
        }
        return $this->redirect($this->referer());
    }


    public function moveup($id) { 
        $this->isAdminUser();     
        $this->autoRender=false;
        $help = $this->Helps->get($id, ['contain' => []]);
        if (!empty($help)){
            if($this->Helps->moveDown($help)==false) {
                $this->Flash->error(__('Sort failed'));
            }
        } else {
            $this->Flash->error(__('Sort failed'));
        }
        return $this->redirect($this->referer());
    }
    
    public function movedown($id) {
        $this->isAdminUser();     
        $this->autoRender=false;
        $help = $this->Helps->get($id, ['contain' => []]);
        if (!empty($help)){
            if($this->Helps->moveUp($help)==false) {
                $this->Flash->error(__('Sort failed'));
            }
        } else {
            $this->Flash->error(__('Sort failed'));
        }
        return $this->redirect($this->referer());
    }

    private function siteOptions() {
        return ['home' => __('Home Page'), 'create' => __('User Create Page'), 'password' => __('Password Recover Page')];
    }


}
