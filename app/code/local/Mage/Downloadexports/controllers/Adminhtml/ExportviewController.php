<?php

class Mage_Downloadexports_Adminhtml_ExportviewController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
    
    public function downloadAction()
    {
        $post = $this->getRequest()->getParams();
//        var_dump($post['name']); exit;


        try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
            }
				$v = $post['name'];
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\n");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Content-type: text/csv;\n");
				header("Content-Transfer-Encoding: binary");
				header("Cache-Control: no-cache");
				header("Expires: -1");
				header("Content-Disposition: attachment; filename=\"$v\";\n\n");
				echo file_get_contents(Mage::getBaseDir('var').'/export/'.$v);
				die();

            
            $message = $this->__('Your form has been submitted successfully.');
//            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        } catch (Exception $e) {
            Mage::log($e->getMessage(),null,'download.log');
//            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*');
    }
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('downloadexports/grid')->toHtml()
        );
    }

    public function deleteAction() {
    	$file = $this->getRequest()->getParam('name');
		if(unlink(Mage::getBaseDir('var').'/export/'.$file))
	    	$this->_getSession()->addSuccess($this->__("Deleted file $file successfully"));
		else
	    	$this->_getSession()->addError($this->__("Could not delete file $file"));
		
    	$this->_redirect('*/*');		
    }
    
    public function massDeleteAction() {
		$deleteFiles = $this->getRequest()->getParam($this->getRequest()->getParam('massaction_prepare_key'));
    	$successful = 0;
    	$failed = 0;
		
		if($deleteFiles) {
			foreach($deleteFiles as $fileName) {
				$result = unlink(Mage::getBaseDir('var').'/export/'.$fileName);
    			if($result) {
    				$successful++;
    			} else {
    				$failed++;
    			}
			}
			if($successful>0)
				$this->_getSession()->addSuccess($this->__("Deleted $successful files successfully"));
			if($failed>0)
				$this->_getSession()->addSuccess($this->__("Unable to delete $failed files"));
		}
    	$this->_redirect('*/*');		
    }
    
    public function massDownloadAction() {
		$dlFiles = $this->getRequest()->getParam($this->getRequest()->getParam('massaction_prepare_key'));
    
		if($dlFiles) {
			$zip_str = '';
			foreach($dlFiles as $fileName)
				$zip_str .= Mage::getBaseDir('var').'/export/'.$fileName.' ';
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT\n");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Content-type: application/zip;\n");
			header("Content-Transfer-Encoding: binary");
			header("Cache-Control: no-cache");
			header("Expires: -1");
			header("Content-Disposition: attachment; filename=\"export_".date('m-d-Y').".zip\";\n\n");
			die(passthru("zip -j - $zip_str | cat"));				
		}
    }
}
