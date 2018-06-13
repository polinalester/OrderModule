<?php 
namespace Order\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;
use VuFind\Exception\RecordMissing as RecordMissingException;

class Order implements InputFilterAwareInterface
{
    public $id;
    public $user_id;
    public $book_id;
    public $ordered;
    public $finished;
    public $status;
    public $record;

    private $inputFilter;

    public function isDate($str)
    {
        return is_numeric(strtotime($str));
    }

    public function exchangeArray(array $data)
    {
        $this->id     = !empty($data['id']) ? $data['id'] : null;
        $this->user_id = !empty($data['user_id']) ? $data['user_id'] : null;
        $this->book_id  = !empty($data['book_id']) ? $data['book_id'] : null;
        if ( !preg_match('Books[0-9]{8}/', $this->book_id) ||  !preg_match('Articles[0-9]{8}/', $this->book_id) ){
					//do smth
		}
		
		$this->ordered  = !empty($data['ordered']) ? $data['ordered'] : null;
		$this->finished  = !empty($data['finished']) ? $data['finished'] : null;
        $this->status = !empty($data['status']) ? $data['status'] : null;
        try {
            //$source = $this->params()->fromPost('source', $this->params()->fromQuery('source', DEFAULT_SEARCH_BACKEND));
            $this->record = $this->getRecordLoader()->load($this->book_id);
        } catch(RecordMissingException $e){
            $this->record = null;
        }

    }
    public function getArrayCopy()
    {
        return [
            'id'     => $this->id,
            'user_id' => $this->user_id,
            'book_id'  => $this->book_id,
            'ordered'  => $this->ordered,
            'finished'  => $this->finished,
            'status'  => $this->status,
            'record' => $this->record,
        ];
    }
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'user_id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'book_id',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'title',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }

}