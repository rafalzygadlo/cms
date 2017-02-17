<?php

include 'models/pageModel.php';
include 'views/pageView.php';

class pageCtrl extends Ctrl
{

    public function __construct()
    {
        parent::__construct(false);

        $this->Model = new pageModel();
        $this->View = new pageView($this);
        $this->View->ViewTitle = $this->Msg('_PAGE_','Page');
        $this->View->CtrlName = CTRL_PAGE;
        
        
        $UrlQuery = basename($_SERVER['REQUEST_URI']);
        
        if ($UrlQuery=='' OR $UrlQuery=='page' OR $UrlQuery=='home')
        {
        
            $this->View->HomePage = TRUE;
            
        }
            
    }
	
	public function ReadSession()
    {
        $this->View->_Id = Session::GetId();
        $this->View->_IdParent = Session::GetIdParent();
        //$this->View->Page = Session::GetPage();
        $this->View->Limit = Session::GetLimit();
        $this->View->OrderColumnId = Session::GetOrderColumnId();
        $this->View->Asc = Session::GetAsc();
    }
    
	public function FindPage()
	{
		// próba odszukania strony po url
		// szukamy wtedy kiedy nie jest to opcja i ustalona jest metoda
		
		if($this->Option)
        {
			
            $this->Page = Session::GetCurrentPage();
        
		}else{
            
			// kiedy ctrl jest w url
            if(isset($this->Method))
            {
                $this->Page = $this->Method;
            }   
        
		}
		
		//print 'PAGE'.$this->Page;
		//print 'METHOD'.$this->Method;
		if(empty($this->Page))
		{
			$this->View->_IdParent = 0;
		
		}else{
			
			$page = $this->Model->FindPage($this->Page);
			if($page)
			{
				$this->SetIdParent($page->id_page);
				Session::SetCurrentPage($this->Page);
			}else{
				//$this->View->Render('exception/index.html');
				new myException('404','Not Found');
			}
		}
		
	}
    
	// nadpisany Index inny template wyświetlania
    // tutaj ma czytać z bazy jaki ma być template dla strony i renderować
    	
	public function Listing()
    {
		//metoda index znaczy się że nie wybrana żadna strona ustawiamy na główną
		// a model sobie wyszuka stronę startową
	
		$this->View->SetColumns();
		$this->View->Asc = SORT_ASC;
        $this->View->SetModel($this->Model);
		$this->View->SetItems($this->Model);
        // set template			
		
		$page = $this->View->CurrentItem;
		
        if($page)
        {
			$template = $page->template;
			$this->View->PageTitle = $page->title; 
            if($this->IsTemplateExists($template))
                $this->View->Render($template);
            else
                $this->View->Render('gridView');
         
		}
    }

}
