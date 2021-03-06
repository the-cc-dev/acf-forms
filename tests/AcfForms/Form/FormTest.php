<?php
namespace Trendwerk\AcfForms\Test\Form;

use Trendwerk\AcfForms\Form\Form;
use Trendwerk\AcfForms\Form\Forms;
use Trendwerk\AcfForms\Test\TestCase;

class FormTest extends TestCase
{
    private $fieldGroup = 'testFieldGroup';
    private $form = 'testForm';
    private $forms;

    public function setUp()
    {
        parent::setUp();

        $this->createFieldGroup($this->fieldGroup);

        $this->forms = Forms::getInstance();
        $this->forms->add($this->form, [
            'acfForm'          => [
                'field_groups' => [$this->fieldGroup],
            ],
        ]);
    }

    public function testFieldGroup()
    {
        $this->assertNotNull($this->getFieldGroup($this->fieldGroup));
    }

    public function testHead()
    {
        ob_start();
        Form::head();
        wp_enqueue_scripts();
        wp_print_scripts();
        $output = ob_get_clean();

        $this->assertContains('acf-input.min.js', $output);
        $this->assertContains('acf-pro-input.min.js', $output);
    }

    public function testRender()
    {
        $form = new Form($this->form);

        ob_start();
        $form->render();
        $output = ob_get_clean();

        $this->assertContains('<form', $output);
        $this->assertContains('<input type="hidden" name="form" value="' . $this->form . '" />', $output);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->forms->remove($this->form);
        $this->destroyFieldGroup($this->fieldGroup);
    }
}
