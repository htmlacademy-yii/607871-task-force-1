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

        $actual = \App\Task::getStatusMapping();

        $this->assertEquals($expected, $actual);
    }

    public function testGetActionMapping()
    {
        $expected = [
            'volunteer' => 'Откликнуться',
            'cancel' => 'Отменить',
            'done' => 'Выполнено',
            'refuse' => 'Отказаться',
        ];

        $actual = \App\Task::getActionMapping();

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
            $actual = \App\Task::getStatusByAction($action);
            $this->assertEquals($status, $actual);
        }
    }

    public function testGetPossibleActions()
    {
        $map = [
            'new' => ['cancel', 'volunteer' ],
            'in progress' => ['done', 'refuse'],
            'finished' => null,
        ];

        foreach ($map as $status => $action) {
            $actual = \App\Task::getPossibleActions($status);
            $this->assertEquals($map[$status], $actual);
        }

    }
}
