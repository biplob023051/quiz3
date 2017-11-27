<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Helps Controller
 *
 * @property \App\Model\Table\HelpsTable $Helps
 */
class HelpsController extends AppController
{
    public function index() {
        $this->set('title_for_layout',__('HELP_HEADER'));
        $helps = $this->Helps->parentsOptions();
        foreach ($helps as $parent_id => $value) {
            $helps[$value] = $this->Helps->find('all', array(
                'conditions' => array(
                    'Helps.status' => 1, 
                    'Helps.parent_id' => $parent_id,
                    'Helps.type' => 'help'
                ),
                'order' => array(
                        'Helps.lft'=>' DESC',
                        'Helps.rght'=>' ASC',
                    )
                )
            )->toArray();
            unset($helps[$parent_id]);
        }
        $this->set(compact('helps'));
    }
}
