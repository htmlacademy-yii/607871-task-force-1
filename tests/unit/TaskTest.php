<?php

class TaskTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests

    public function testGetStatusMapping()
    {
        $expected = [
            'new' => 'Новое',
            'canceled' => 'Отменено',
            'in progress' => 'В работе',
            'failed' => 'Провалено',
            'finished' => 'Выполнено',
        ];

        $actual = \App\business\Task::getStatusMapping();

        $this->assertEquals($expected, $actual);
    }

       public function testGetStatusByAction()
    {
        $map = [
            'cancel' => 'canceled',
            'done' => 'finished',
            'refuse' => 'failed',
            'volunteer' => 'in progress',
            'test' => null,
        ];

        foreach ($map as $action => $status) {
            $actual = \App\business\Task::getStatusByAction($action);
            $this->assertEquals($status, $actual);
        }
    }

    public function testGetPossibleActions()
    {
        $map = [
            ['status' =>'new', 'clientId' => 1, 'executorId' => null, 'userId' => 2, 'action' => ['volunteer']],
            ['status' =>'new', 'clientId' => 1, 'executorId' => null, 'userId' => 1, 'action' => ['cancel']],
            ['status' =>'in progress', 'clientId' => 1, 'executorId' => 2, 'userId' => 2, 'action' => ['refuse']],
            ['status' =>'in progress', 'clientId' => 1, 'executorId' => 2, 'userId' => 1, 'action' => ['done']],
            ['status' =>'in progress', 'clientId' => 1, 'executorId' => 3, 'userId' => 2, 'action' => null],
            ['status' =>'finished', 'clientId' => 1, 'executorId' => 2, 'userId' => 2, 'action' => null],
            ['status' =>'finished', 'clientId' => 1, 'executorId' => 2, 'userId' => 1, 'action' => null],
            ['status' =>'finished', 'clientId' => 1, 'executorId' => 3, 'userId' => 2, 'action' => null],
        ];

        foreach ($map as $taskState) {
            $actual = \App\business\Task::getPossibleActions($taskState['status'], $taskState['clientId'], $taskState['executorId'], $taskState['userId']);
            $this->assertEquals($taskState['action'], $actual);
        }

    }


}
