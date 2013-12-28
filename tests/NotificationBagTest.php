<?php

use Mockery as m;

require_once 'Mocks/NotificationsBagMock.php';

class NotificationBagTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();

        \Krucas\Notification\NotificationsBag::unsetEventDispatcher();
    }

    public function testIsConstructed()
    {
        $notificationBag = $this->getNotificationBag();
        $this->assertEquals('test', $notificationBag->getName());
        $this->assertEquals(array(), $notificationBag->getTypes());
        $this->assertNull($notificationBag->getDefaultFormat());
    }

    public function testAddType()
    {
        $notificationBag = $this->getNotificationBag();
        $this->assertEquals(array(), $notificationBag->getTypes());

        $notificationBag->addType('warning');
        $this->assertEquals(array('warning'), $notificationBag->getTypes());
    }

    public function testAddTypesArray()
    {
        $notificationBag = $this->getNotificationBag();
        $this->assertEquals(array(), $notificationBag->getTypes());

        $notificationBag->addType(array('info', 'danger'));
        $this->assertEquals(array('info', 'danger'), $notificationBag->getTypes());
    }

    public function testAddTypesArrayAsIndividualParam()
    {
        $notificationBag = $this->getNotificationBag();
        $this->assertEquals(array(), $notificationBag->getTypes());

        $notificationBag->addType('info', 'danger');
        $this->assertEquals(array('info', 'danger'), $notificationBag->getTypes());
    }

    public function testAddExistingType()
    {
        $notificationBag = $this->getNotificationBag();

        $notificationBag->addType('warning');
        $this->assertEquals(array('warning'), $notificationBag->getTypes());

        $notificationBag->addType('warning');
        $this->assertEquals(array('warning'), $notificationBag->getTypes());
    }

    public function testCheckIfTypeIsAvailable()
    {
        $notificationBag = $this->getNotificationBag();
        $this->assertFalse($notificationBag->typeIsAvailable('warning'));

        $notificationBag->addType('warning');
        $this->assertTrue($notificationBag->typeIsAvailable('warning'));
    }

    public function testClearTypes()
    {
        $notificationBag = $this->getNotificationBag();
        $this->assertEquals(array(), $notificationBag->getTypes());

        $notificationBag->addType('warning');
        $this->assertEquals(array('warning'), $notificationBag->getTypes());

        $notificationBag->clearTypes();
        $this->assertEquals(array(), $notificationBag->getTypes());
    }

    public function testExtractType()
    {
        $notificationBag = $this->getNotificationBag();
        $this->assertFalse($notificationBag->extractType('info'));

        $notificationBag->addType(array('info', 'success'));
        $this->assertEquals(array('info', 'add'), $notificationBag->extractType('info'));
        $this->assertEquals(array('info', 'instant'), $notificationBag->extractType('infoInstant'));
        $this->assertEquals(array('info', 'clear'), $notificationBag->extractType('clearInfo'));
        $this->assertEquals(array('info', 'show'), $notificationBag->extractType('showInfo'));
        $this->assertEquals(array('success', 'add'), $notificationBag->extractType('success'));
    }

    public function testExtractTypeInvalid()
    {
        $notificationBag = $this->getNotificationBag();

        $notificationBag->addType(array('info', 'success'));
        $this->assertFalse($notificationBag->extractType('ShowInfo'));
        $this->assertFalse($notificationBag->extractType('infoinstant'));
    }

    public function testSetDefaultFormat()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->setDefaultFormat(':type - :message');
        $this->assertEquals(':type - :message', $notificationBag->getDefaultFormat());
    }

    public function testSetFormatForType()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('success');
        $this->assertFalse($notificationBag->getFormat('success'));

        $notificationBag->setFormat('success', 'OK - :message');
        $this->assertEquals('OK - :message', $notificationBag->getFormat('success'));
    }

    public function testSetFormatsArray()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertFalse($notificationBag->getFormat('success'));
        $this->assertFalse($notificationBag->getFormat('info'));

        $notificationBag->setFormats(
            array(
                'success'   => 'OK - :message',
                'info'      => 'INFO - :message',
            )
        );
        $this->assertEquals('OK - :message', $notificationBag->getFormat('success'));
        $this->assertEquals('INFO - :message', $notificationBag->getFormat('info'));
    }

    public function testSetFormatForNonExistingType()
    {
        $notificationBag = $this->getNotificationBag();
        $this->assertFalse($notificationBag->getFormat('success'));

        $notificationBag->setFormat('success', 'OK - :message');
        $this->assertFalse($notificationBag->getFormat('success'));
    }

    public function testGetFormatWhenJustDefaultIsSet()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('success');
        $this->assertFalse($notificationBag->getFormat('success'));
        $notificationBag->setDefaultFormat(':type - :message');
        $this->assertEquals(':type - :message', $notificationBag->getFormat('success'));
    }

    public function testClearFormat()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertFalse(false, $notificationBag->getFormat('success'));
        $this->assertFalse(false, $notificationBag->getFormat('info'));

        $notificationBag->setFormats(
            array(
                'success'   => 'OK - :message',
                'info'      => 'INFO - :message',
            )
        );
        $this->assertEquals('OK - :message', $notificationBag->getFormat('success'));
        $this->assertEquals('INFO - :message', $notificationBag->getFormat('info'));

        $notificationBag->clearFormat('success');
        $this->assertFalse($notificationBag->getFormat('success'));
        $this->assertEquals('INFO - :message', $notificationBag->getFormat('info'));
    }

    public function testClearAllFormats()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertFalse(false, $notificationBag->getFormat('success'));
        $this->assertFalse(false, $notificationBag->getFormat('info'));

        $notificationBag->setFormats(
            array(
                'success'   => 'OK - :message',
                'info'      => 'INFO - :message',
            )
        );
        $this->assertEquals('OK - :message', $notificationBag->getFormat('success'));
        $this->assertEquals('INFO - :message', $notificationBag->getFormat('info'));

        $notificationBag->clearFormats();
        $this->assertFalse($notificationBag->getFormat('success'));
        $this->assertFalse($notificationBag->getFormat('info'));
    }

    public function testAddMessage()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('success');
        $notificationBag->setDefaultFormat(':message');
        $notificationBag->setEventDispatcher($events = m::mock('Illuminate\Events\Dispatcher'));
        $message = new \Krucas\Notification\Message('success', 'test', true, ':message', null, null);
        $events->shouldReceive('fire')->once()->with('notification.flash: test', array($notificationBag, $message));
        $notificationBag->add('success', 'test');
    }

    public function testAddMessageWithCustomFormat()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('success');
        $this->assertCount(0, $notificationBag);

        $notificationBag->add('success', 'test', false, 'customFormat');
        $this->assertCount(1, $notificationBag);
        $this->assertEquals('customFormat', $notificationBag->all()[0]->getFormat());
    }

    public function testAddInstantMessageUsingNamedMethod()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('success');
        $this->assertCount(0, $notificationBag);

        $notificationBag->successInstant('test');
        $this->assertCount(1, $notificationBag);
    }

    public function testAddInstantMessageEventFired()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('info');
        $notificationBag->setDefaultFormat(':message');
        $notificationBag->setEventDispatcher($events = m::mock('Illuminate\Events\Dispatcher'));
        $message = new \Krucas\Notification\Message('info', 'test', false, ':message', null, null);
        $events->shouldReceive('fire')->once()->with('notification.added: test', array($notificationBag, $message));
        $notificationBag->infoInstant('test');
    }

    public function testAddFlashMessageUsingNamedMethod()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('success');
        $notificationBag->setDefaultFormat(':message');
        $notificationBag->setEventDispatcher($events = m::mock('Illuminate\Events\Dispatcher'));
        $message = new \Krucas\Notification\Message('success', 'test', true, ':message', null, null);
        $events->shouldReceive('fire')->once()->with('notification.flash: test', array($notificationBag, $message));
        $notificationBag->success('test');
    }

    public function testAddMessageForNonExistingType()
    {
        $notificationBag = $this->getNotificationBag();
        $this->assertCount(0, $notificationBag);

        $notificationBag->add('success', 'test', false);
        $this->assertCount(0, $notificationBag);
    }

    public function testAddMessagesForMultipleTypes()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertCount(0, $notificationBag);

        $notificationBag->add('success', 'test', false);
        $notificationBag->add('info', 'test', false);
        $this->assertCount(2, $notificationBag);
    }

    public function testAddMessagesForMultipleTypesUsingNamedMethods()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertCount(0, $notificationBag);

        $notificationBag->add('success', 'test', false);
        $notificationBag->add('info', 'test', false);
        $this->assertCount(2, $notificationBag);
    }

    public function testAddInstantMessageWithMessageInstance()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('info');
        $this->assertCount(0, $notificationBag);

        $message = $this->getMessage();
        $message->shouldReceive('setType')->with('info')->andReturn($message);
        $message->shouldReceive('isFlashable')->andReturn(false);
        $message->shouldReceive('getPosition')->andReturn(null);
        $notificationBag->add('info', $message, false);
        $this->assertCount(1, $notificationBag);
    }

    public function testAddFlashMessageWithMessageInstance()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('info');
        $notificationBag->setEventDispatcher($events = m::mock('Illuminate\Events\Dispatcher'));
        $message = $this->getMessage();
        $message->shouldReceive('setType')->with('info')->andReturn($message);
        $message->shouldReceive('isFlashable')->andReturn(true);
        $message->shouldReceive('getPosition')->andReturn(null);
        $events->shouldReceive('fire')->once()->with('notification.flash: test', array($notificationBag, $message));
        $this->assertCount(0, $notificationBag);

        $notificationBag->add('info', $message);
        $this->assertCount(0, $notificationBag);
    }

    public function testAddInstantMessageWithMessageInstanceUsingNamedMethods()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('info');
        $this->assertCount(0, $notificationBag);

        $message = $this->getMessage();
        $message->shouldReceive('setType')->with('info')->andReturn($message);
        $message->shouldReceive('isFlashable')->andReturn(false);
        $message->shouldReceive('getPosition')->andReturn(null);
        $notificationBag->infoInstant($message);
        $this->assertCount(1, $notificationBag);
    }

    public function testAddInstantMessageWithMessageInstanceUsingNamedMethodsOverrideFlashStatus()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('info');
        $this->assertCount(0, $notificationBag);

        $message = $this->getMessage();
        $message->shouldReceive('setType')->with('info')->andReturn($message);
        $message->shouldReceive('isFlashable')->andReturn(true, false);
        $message->shouldReceive('setFlashable')->with(false)->andReturn($message);
        $message->shouldReceive('getPosition')->andReturn(null);
        $notificationBag->infoInstant($message);
        $this->assertCount(1, $notificationBag);
    }

    public function testAddInstantMessageWithMessageInstanceAndOverrideFormat()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('info');
        $this->assertCount(0, $notificationBag);

        $message = $this->getMessage();
        $message->shouldReceive('setType')->with('info')->andReturn($message);
        $message->shouldReceive('isFlashable')->andReturn(false);
        $message->shouldReceive('setFormat')->with(':message')->andReturn($message);
        $message->shouldReceive('getPosition')->andReturn(null);
        $notificationBag->add('info', $message, false, ':message');
        $this->assertCount(1, $notificationBag);
    }

    public function testAddMessageAtPosition()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('info');
        $this->assertCount(0, $notificationBag);

        $message = $this->getMessage();
        $message->shouldReceive('setType')->with('info')->andReturn($message);
        $message->shouldReceive('isFlashable')->andReturn(false);
        $message->shouldReceive('getPosition')->andReturn(5);
        $notificationBag->add('info', $message, false);
        $this->assertCount(1, $notificationBag);
        $this->assertEquals($message, $notificationBag->getAtPosition(5));
    }

    public function testAddMessagesAtSamePosition()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('info');
        $this->assertCount(0, $notificationBag);

        $message1 = $this->getMessage();
        $message1->shouldReceive('setType')->with('info')->andReturn($message1);
        $message1->shouldReceive('isFlashable')->andReturn(false);
        $message1->shouldReceive('getPosition')->andReturn(5);

        $message2 = $this->getMessage();
        $message2->shouldReceive('setType')->with('info')->andReturn($message2);
        $message2->shouldReceive('isFlashable')->andReturn(false);
        $message2->shouldReceive('getPosition')->andReturn(5);

        $notificationBag->add('info', $message1, false);
        $notificationBag->add('info', $message2, false);

        $this->assertCount(2, $notificationBag);
        $this->assertEquals($message2, $notificationBag->getAtPosition(5));
        $this->assertEquals($message1, $notificationBag->getAtPosition(6));
    }

    public function testGetInstantMessagesForGivenType()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertCount(0, $notificationBag);

        $notificationBag->successInstant('test');
        $notificationBag->successInstant('test2');
        $notificationBag->infoInstant('test');
        $this->assertCount(3, $notificationBag);
        $this->assertCount(2, $notificationBag->get('success'));
        $this->assertCount(1, $notificationBag->get('info'));
    }

    public function testClearMessagesForGivenType()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertCount(0, $notificationBag->get('success'));
        $this->assertCount(0, $notificationBag->get('info'));

        $notificationBag->add('success', 'test', false);
        $notificationBag->add('info', 'test', false);
        $this->assertCount(1, $notificationBag->get('success'));
        $this->assertCount(1, $notificationBag->get('info'));

        $notificationBag->clear('success');
        $this->assertCount(0, $notificationBag->get('success'));
        $this->assertCount(1, $notificationBag->get('info'));

        $notificationBag->clear('info');
        $this->assertCount(0, $notificationBag->get('success'));
        $this->assertCount(0, $notificationBag->get('info'));
    }

    public function testClearMessagesForGivenTypeUsingNamedMethod()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertCount(0, $notificationBag->get('success'));
        $this->assertCount(0, $notificationBag->get('info'));

        $notificationBag->add('success', 'test', false);
        $notificationBag->add('info', 'test', false);
        $this->assertCount(1, $notificationBag->get('success'));
        $this->assertCount(1, $notificationBag->get('info'));

        $notificationBag->clearSuccess();
        $this->assertCount(0, $notificationBag->get('success'));
        $this->assertCount(1, $notificationBag->get('info'));

        $notificationBag->clearInfo();
        $this->assertCount(0, $notificationBag->get('success'));
        $this->assertCount(0, $notificationBag->get('info'));
    }

    public function testClearAllMessages()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertCount(0, $notificationBag->get('success'));
        $this->assertCount(0, $notificationBag->get('info'));

        $notificationBag->add('success', 'test', false);
        $notificationBag->add('info', 'test', false);
        $this->assertCount(1, $notificationBag->get('success'));
        $this->assertCount(1, $notificationBag->get('info'));

        $notificationBag->clearAll();
        $this->assertCount(0, $notificationBag->get('success'));
        $this->assertCount(0, $notificationBag->get('info'));
    }

    public function testClearAllMessageWithoutGivenType()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertCount(0, $notificationBag->get('success'));
        $this->assertCount(0, $notificationBag->get('info'));

        $notificationBag->add('success', 'test', false);
        $notificationBag->add('info', 'test', false);
        $this->assertCount(1, $notificationBag->get('success'));
        $this->assertCount(1, $notificationBag->get('info'));

        $notificationBag->clear();
        $this->assertCount(0, $notificationBag->get('success'));
        $this->assertCount(0, $notificationBag->get('info'));
    }

    public function testGetAllMessages()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertCount(0, $notificationBag);

        $notificationBag->add('success', 'test', false);
        $notificationBag->add('info', 'test', false);
        $this->assertCount(2, $notificationBag->all());
    }

    public function testGetFirstMessage()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertCount(0, $notificationBag);
        $this->assertNull($notificationBag->first());

        $notificationBag->add('success', 'test', false);
        $notificationBag->add('info', 'test', false);
        $this->assertCount(2, $notificationBag);
        $this->assertEquals('success', $notificationBag->first()->getType());
        $this->assertEquals('test', $notificationBag->first()->getMessage());
    }

    public function testShowMessagesForGivenType()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $notificationBag->setDefaultFormat(':type - :message');
        $this->assertCount(0, $notificationBag);

        $notificationBag->add('success', 'test', false);
        $notificationBag->add('info', 'test', false);
        $this->assertCount(2, $notificationBag);
        $this->assertEquals('success - test', $notificationBag->show('success'));
        $this->assertEquals('info - test', $notificationBag->show('info'));
    }

    public function testShowMessagesForGivenTypeWithCustomFormat()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('success');
        $notificationBag->setDefaultFormat(':type - :message');
        $this->assertCount(0, $notificationBag);

        $notificationBag->add('success', 'test', false);
        $this->assertCount(1, $notificationBag);
        $this->assertEquals('test - OK', $notificationBag->show('success', ':message - OK'));
    }

    public function testShowMessagesForGivenTypeUsingNamedMethods()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $notificationBag->setDefaultFormat(':type - :message');
        $this->assertCount(0, $notificationBag);

        $notificationBag->add('success', 'test', false);
        $notificationBag->add('info', 'test', false);
        $this->assertCount(2, $notificationBag);
        $this->assertEquals('success - test', $notificationBag->showSuccess());
        $this->assertEquals('info - test', $notificationBag->showInfo());
    }

    public function testShowAllMessages()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $notificationBag->setDefaultFormat(':type - :message');
        $this->assertCount(0, $notificationBag);

        $notificationBag->add('success', 'test', false);
        $notificationBag->add('info', 'test', false);
        $this->assertCount(2, $notificationBag);
        $this->assertEquals('success - testinfo - test', $notificationBag->show());
        $this->assertEquals('success - testinfo - test', $notificationBag->showAll());
    }

    public function testAddTypesForGroupedRendering()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertEquals(array(), $notificationBag->getGroupingForRender());

        $notificationBag->addToGrouping('success');
        $this->assertEquals(array('success'), $notificationBag->getGroupingForRender());

        $notificationBag->addToGrouping('info');
        $this->assertEquals(array('success', 'info'), $notificationBag->getGroupingForRender());
    }

    public function testAddAndRemoveTypesForGroupedRendering()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $this->assertEquals(array(), $notificationBag->getGroupingForRender());

        $notificationBag->addToGrouping('success');
        $this->assertEquals(array('success'), $notificationBag->getGroupingForRender());

        $notificationBag->addToGrouping('info');
        $this->assertEquals(array('success', 'info'), $notificationBag->getGroupingForRender());

        $notificationBag->removeFromGrouping('success');
        $this->assertEquals(array('info'), $notificationBag->getGroupingForRender());
    }

    public function testAddTypesForGroupedRenderingInvalidType()
    {
        $notificationBag = $this->getNotificationBag();
        $this->assertEquals(array(), $notificationBag->getGroupingForRender());

        $notificationBag->addToGrouping('success');
        $this->assertEquals(array(), $notificationBag->getGroupingForRender());
    }

    public function testShowGroupedMessages()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType(array('success', 'info'));
        $notificationBag->setDefaultFormat(':type - :message');
        $notificationBag->add('success', 'test', false);
        $notificationBag->add('info', 'test2', false);
        $this->assertEquals('success - test', $notificationBag->group('success')->show());
        $this->assertEquals('info - test2success - test', $notificationBag->group('info', 'success')->show());
    }

    public function testToArray()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('info');
        $notificationBag->setDefaultFormat(':message');
        $notificationBag->add('info', 'test', false);

        $this->assertEquals(
            array(
                'container'     => 'test',
                'format'        => ':message',
                'types'         => array('info'),
                'notifications' => array(
                    array(
                        'message'   => 'test',
                        'format'    => ':message',
                        'type'      => 'info',
                        'flashable' => false,
                        'alias'     => null,
                        'position'  => null
                    )
                )
            ),
            $notificationBag->toArray()
        );
    }

    public function testToJson()
    {
        $notificationBag = $this->getNotificationBag();
        $notificationBag->addType('info');
        $notificationBag->setDefaultFormat(':message');
        $notificationBag->add('info', 'test', false);

        $this->assertEquals(
            json_encode(
                array(
                    'container'     => 'test',
                    'format'        => ':message',
                    'types'         => array('info'),
                    'notifications' => array(
                        array(
                            'message'   => 'test',
                            'format'    => ':message',
                            'type'      => 'info',
                            'flashable' => false,
                            'alias'     => null,
                            'position'  => null
                        )
                    )
                )
            ),
            $notificationBag->toJson()
        );
    }


    protected function getNotificationBag()
    {
        return new NotificationsBagMock('test');
    }

    protected function getMessage()
    {
        $message = m::mock('Krucas\Notification\Message');
        return $message;
    }
}
