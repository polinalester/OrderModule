<?php

namespace Order\Form;

use Zend\Form\Form;

class OrderForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('order');

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'user_id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'book_id',
            'type' => 'text',
            'options' => [
                'label' => 'Издание',
            ],
        ]);
        $this->add([
            'name' => 'ordered',
            'type' => 'hidden',
            'options' => [
                'label' => 'Дата заказа',
            ],
        ]);
        $this->add([
            'name' => 'finished',
            'type' => 'hidden',
            'options' => [
                'label' => 'Дата завершения заказа',
            ],
        ]);
        $this->add([
            'name' => 'status',
            'type' => 'hidden',
            'options' => [
                'label' => 'Статус',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}