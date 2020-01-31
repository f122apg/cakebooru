<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Error\Debugger;
use Cake\Core\Configure;

/**
 * Posts Controller
 *
 * @property \App\Model\Table\PostsTable $Posts
 *
 * @method \App\Model\Entity\Post[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PostsController extends AppController
{
    public function initialize() : void
    {
        parent::initialize();

        $this->loadModel('Tags');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->viewBuilder()->setLayout('TwitterBootstrap/posts');

        if (!empty($this->request->getQuery('search'))) {
            //タグ取得
            $search = $this->request->getQuery('search');
            $tags = explode(Configure::read('TagDelimiter'), $search);

            $posts = $this->Posts
                ->find()
                ->contain(['Tags'])
                ->matching('Tags', fn($q) => $q->where(function($exp, $query) {
                    //とりあえず完全一致での検索
                    //一部一致なら、LIKEを使うこと
                    $tagCons = $this->Tags->getTagConditions($this->request->getQuery('search'));
                    $exps = [];

                    foreach ($tagCons as $k => $con) {
                        switch ($k) {
                            case 'NOT':
                                $exps[] = $exp->notIn('Tags.tag', $con);
                                break;
                            case 'AND':
                                $exps[] = $exp->and(array_map(fn($c) => ['Tags.tag' => $c], $con));
                                break;
                            case 'OR':
                                $exps[] = $exp->in('Tags.tag', $con);
                                break;
                            case 'NORMAL':
                                $exps[] = $exp->and(array_map(fn($c) => ['Tags.tag' => $c], $con));
                                break;
                        }
                    }

                    return $exp->and($exps);

                    // return $exp->and([
                    //     ['Tags.tag' => 'hiinu'],
                    //     ['Tags.tag' => 'hiinu-kobo']
                    // ]);

                    //$exp->and($exps);
                }))
                ->all();
            Debugger::dump($posts);
        } else {
            $posts = $this->Posts
                ->find()
                ->contain(['Tags'])
                ->all();
        }

        // $this->paginate = [
        //     'contain' => ['Users'],
        // ];
        // $posts = $this->paginate($this->Posts);
        $tags = $this->Tags->getTagsDistinctByPost($posts);

        $this->set(compact('posts', 'tags'));
    }

    /**
     * View method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $this->viewBuilder()->setLayout('TwitterBootstrap/posts');
        $post = $this->Posts->get($this->request->getQuery('id'), [
            'contain' => ['Tags']
        ]);
        $tags = $this->Tags->getTagsDistinctByPost($post);

        $this->set(compact('post', 'tags'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $post = $this->Posts->newEmptyEntity();
        $this->set(compact('post'));

        if ($this->request->is('post')) {
            try {
                //既存のタグが入力されていれば、idを指定する
                $reqData = $this->request->getData();
                if (isset($reqData['tags'])) {
                    $reqData['tags'] = $this->Tags->createBtmData($reqData['tags']);
                }

                $post = $this->Posts->patchEntity($post, $reqData, [
                    'associated' => [
                        'Tags',
                    ]
                ]);

                //saveに失敗（バリデーションエラーも含む）したら、例外が発行される
                $this->Posts->saveOrFail($post);
                $this->Flash->success(__d('cakebooru', 'Yay! This image uploaded.'));
                return $this->redirect(['action' => 'index']);
            } catch (Exception $ex) {
                $this->Flash->error(__d('cakebooru', 'The post could not be saved. Please, try again.'));
            }
        }
    }

    /**
     * Edit method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $post = $this->Posts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $post = $this->Posts->patchEntity($post, $this->request->getData());
            if ($this->Posts->save($post)) {
                $this->Flash->success(__('The post has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The post could not be saved. Please, try again.'));
        }
        $users = $this->Posts->Users->find('list', ['limit' => 200]);
        $this->set(compact('post', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $post = $this->Posts->get($id);
        if ($this->Posts->delete($post)) {
            $this->Flash->success(__('The post has been deleted.'));
        } else {
            $this->Flash->error(__('The post could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
