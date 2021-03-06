<?php
namespace Trendwerk\AcfForms\Test\Entry;

use Trendwerk\AcfForms\Entry\Entries;
use Trendwerk\AcfForms\Entry\Entry;
use Trendwerk\AcfForms\Form\Forms;
use Trendwerk\AcfForms\Test\TestCase;

class EntryTest extends TestCase
{
    private $postId;

    public function setUp()
    {
        parent::setUp();

        $this->postId = $this->factory->post->create([
            'post_type' => Entries::POST_TYPE,
        ]);
    }

    public function testFind()
    {
        $entry = Entry::find($this->postId);
        $this->assertEquals('Trendwerk\AcfForms\Entry\Entry', get_class($entry));
        $this->assertEquals($this->postId, $entry->getId());
    }

    public function testEmptyFieldGroups()
    {
        $entry = Entry::find($this->postId);
        $this->assertEquals([], $entry->getFieldGroups());
    }

    public function testFieldGroups()
    {
        $fieldGroups = ['testFieldGroup', 'anotherFieldGroup'];

        $entry = Entry::find($this->postId);
        $entry->setFieldGroups($fieldGroups);
        $this->assertEquals($fieldGroups, $entry->getFieldGroups());
    }

    public function testGetField()
    {
        $field = 'testField';
        $value = 'testValue';

        update_field($field, $value, $this->postId);

        $entry = Entry::find($this->postId);

        $this->assertEquals($entry->getField($field), $value);
    }

    public function testForm()
    {
        $form = 'contact';

        $entry = Entry::find($this->postId);
        $entry->setForm($form);

        $this->assertEquals($entry->getForm(), $form);
    }

    public function testTitle()
    {
        $form = 'contact';
        $label = 'Contact';

        $date = get_the_date(null, $this->postId);
        $time = get_the_time(null, $this->postId);

        $forms = Forms::getInstance();
        $forms->add($form, [
            'acfForm'          => [
                'field_groups' => [],
            ],
            'label'            => $label,
        ]);

        $entry = Entry::find($this->postId);
        $entry->setForm($form);

        $this->assertContains($label, $entry->getTitle());
        $this->assertContains($date, $entry->getTitle());
        $this->assertContains($time, $entry->getTitle());

        $forms->remove($form);
    }

    public function testTitleNoLabel()
    {
        $form = 'contact';

        $forms = Forms::getInstance();
        $forms->add($form, [
            'acfForm'          => [
                'field_groups' => [],
            ],
        ]);

        $entry = Entry::find($this->postId);
        $entry->setForm($form);

        $this->assertContains($form, $entry->getTitle());

        $forms->remove($form);
    }

    public function testTitleInvalidForm()
    {
        $form = 'contact';

        $entry = Entry::find($this->postId);
        $entry->setForm($form);

        $this->assertContains($form, $entry->getTitle());
    }
}
