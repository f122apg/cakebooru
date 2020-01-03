<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * PostCounts Controller
 *
 * @property \App\Model\Table\PostCountsTable $PostCounts
 *
 * @method \App\Model\Entity\PostCount[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PostCountsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $postCounts = $this->paginate($this->PostCounts);

        $this->set(compact('postCounts'));
    }

    /**
     * View method
     *
     * @param string|null $id Post Count id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $postCount = $this->PostCounts->get($id, [
            'contain' => [],
        ]);

        $this->set('postCount', $postCount);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $postCount = $this->PostCounts->newEmptyEntity();
        if ($this->request->is('post')) {
            $postCount = $this->PostCounts->patchEntity($postCount, $this->request->getData());
            if ($this->PostCounts->save($postCount)) {
                $this->Flash->success(__('The post count has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The post count could not be saved. Please, try again.'));
        }
        $this->set(compact('postCount'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Post Count id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $postCount = $this->PostCounts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $postCount = $this->PostCounts->patchEntity($postCount, $this->request->getData());
            if ($this->PostCounts->save($postCount)) {
                $this->Flash->success(__('The post count has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The post count could not be saved. Please, try again.'));
        }
        $this->set(compact('postCount'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Post Count id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $postCount = $this->PostCounts->get($id);
        if ($this->PostCounts->delete($postCount)) {
            $this->Flash->success(__('The post count has been deleted.'));
        } else {
            $this->Flash->error(__('The post count could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
