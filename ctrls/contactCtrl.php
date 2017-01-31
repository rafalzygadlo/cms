<?php

/**
 * contactCtrl
 * 
 * @category   Controller
 * @package    CMS
 * @author     Rafał Żygadło <rafal@maxkod.pl>
 
 * @copyright  2016 maxkod.pl
 * @version    1.0
 */

include 'models/contactModel.php';
include 'views/contactView.php';

class contactCtrl extends Ctrl
{

    public function __construct()
    {
        parent::__construct(false);

        $this->View = new contactView();
 
        $this->View->ViewTitle = $this->Msg('_CONTACT_', 'Contact');
        $this->View->CtrlName = CTRL_CONTACT;
        
        $this->Model = new contactModel(); 
        $this->Validator = new Validator();

        $this->InitFormFields();
        $this->InitRequired();
        $this->InitValidatorFields();
    }

    private function InitFormFields()
    {
        $this->View->Email = new Input();
        $this->View->FirstName = new Input();
        $this->View->LastName = new Input();
        $this->View->Phone = new Input();
        $this->View->Subject = new Input();
        $this->View->Message = new Input();
        $this->View->Newsletter = new Input();
        $this->View->Newsletter->Value = true;
    }
    
    private function ClearFormFields()
    {
        $this->View->Email->Value = NULL;
        $this->View->FirstName->Value = NULL;
        $this->View->LastName->Value = NULL;
        $this->View->Phone->Value = NULL;
        $this->View->Subject->Value = NULL;
        $this->View->Message->Value = NULL;
    }

    private function InitRequired()
    {
        $this->View->Email->SetRequired(true);
    }

    private function InitValidatorFields()
    {
        $this->Validator->Add($this->View->Email);
    }
    
    public function ReadForm()
    {
        $this->View->Email->Value = filter_input(INPUT_POST, CONTACT_EMAIL);
        $this->View->Phone->Value = filter_input(INPUT_POST, CONTACT_PHONE);
        $this->View->FirstName->Value = filter_input(INPUT_POST, CONTACT_FIRST_NAME);
        $this->View->LastName->Value = filter_input(INPUT_POST, CONTACT_LAST_NAME);
        $this->View->Subject->Value = filter_input(INPUT_POST, CONTACT_SUBJECT);
        $this->View->Message->Value = filter_input(INPUT_POST, CONTACT_MESSAGE);
        $this->View->Newsletter->Value = filter_input(INPUT_POST, CONTACT_NEWSLETTER);
    }

    public function Insert()
    {
        $this->InsertContact();
        //$this->InsertCustomer(); zrezygnowałem z dodawania użytkownika do bazy 

        $email = new Email();
        $msg  = "<a href=mailto:".$this->View->Email->Value.">".$this->View->Email->Value."</a><br>";
        $msg .= $this->Msg('_FIRST_NAME_','First Name').': '. $this->View->FirstName->Value."<br>";
        $msg .= $this->Msg('_LAST_NAME_','Last Name').': '.$this->View->LastName->Value."<br>";
        $msg .= $this->Msg('_PHONE_','Phone').': '.$this->View->Phone->Value."<br>";
        $msg .= $this->Msg('_SUBJECT_','Subject').': '.$this->View->Subject->Value."<br>";
        $msg .= $this->Msg('_MESSAGE_','Message').'<br>'.$this->View->Message->Value."<br>";
        
        $email->Send(SMTP_TO, $this->View->Subject->Value,$msg);   
    }
    
    public function InsertContact()
    {
        $this->Model->email = $this->View->Email->Value;
        $this->Model->first_name = $this->View->FirstName->Value;
        $this->Model->last_name = $this->View->LastName->Value;
        $this->Model->phone = $this->View->Phone->Value;
        $this->Model->subject = $this->View->Subject->Value;
        $this->Model->message = $this->View->Message->Value;
        
        if($this->View->Newsletter->Value == 'on')
            $this->Model->newsletter = true;
        else
            $this->Model->newsletter = false;
        
        $this->Model->Insert();
    }
        
    public function InsertCustomer()
    {
        $customer = new customerModel();
        
        $customer->email = $this->View->Email->Value;
        $customer->first_name = $this->View->FirstName->Value;
        $customer->last_name = $this->View->LastName->Value;
        $customer->phone = $this->View->Phone->Value;
        
        $item = $customer->EmailExists(); 
                
        if($item == NULL)
        {
            $customer->Insert();
        }
    }
    
    public function FormAdd()
    {
        $this->View->Title = $this->Msg('_NEW_','New');
        $this->View->Render('contact/index');
    }
    
    public function Listing()
    {
        $this->ClearFormFields();
        $this->View->Render('contact/index');
    }

}