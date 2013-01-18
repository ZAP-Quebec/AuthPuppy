<?php

// +------------------------------------------------------------------------+
// | AuthPuppy Authentication Server                                        |
// | ===============================                                        |
// |                                                                        |
// | AuthPuppy is the new generation of authentication server for           |
// | a wifidog based captive portal suite                                   |
// +------------------------------------------------------------------------+
// | PHP version 5 required.                                                |
// +------------------------------------------------------------------------+
// | Homepage:     http://www.authpuppy.org/                                |
// | Launchpad:    http://www.launchpad.net/authpuppy                       |
// +------------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify   |
// | it under the terms of the GNU General Public License as published by   |
// | the Free Software Foundation; either version 2 of the License, or      |
// | (at your option) any later version.                                    |
// |                                                                        |
// | This program is distributed in the hope that it will be useful,        |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
// | GNU General Public License for more details.                           |
// |                                                                        |
// | You should have received a copy of the GNU General Public License along|
// | with this program; if not, write to the Free Software Foundation, Inc.,|
// | 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.            |
// +------------------------------------------------------------------------+


/**
 * node actions.
 *
 * @package    authpuppy
 * @author     Philippe April <philippe@philippeapril.com>
 * @author     Geneviève Bastien <gbastien@versatic.net>
 * @copyright  2010
 * @version    $Version: 0.1.0$
 */
class nodeActions extends apActions
{
  
  /**
   * Get the filter array of values used to display the list
   */  
  protected function getFilter()
  {
    return $this->getUser()->getAttribute('apAuthLocalUser.filters', array());
  }

  /**
   * Sets the array of values used to display the list
   * @param unknown_type $filters
   */
  protected function setFilter(array $filters)
  {
    return $this->getUser()->setAttribute('apAuthLocalUser.filters',$filters);
  }
  
  public function executeIndex(sfWebRequest $request)
  {
    $query = $this->nodes = Doctrine_Query::create()
      ->select('n.*, (select count(*) from connections where node_id = n.id and status = \''.Connection::$TOKEN_VALIDATED.'\') as num_active_connections')
      ->from('Node n')
      ->orderBy('n.name');
      
    // This is not a navigation, so the user came from somewhere else, we reset the filter
    if ($request->getParameter('page', 0) < 1) {
      $this->setFilter(array());
    }
    
    // Create the filter form 
    $this->filter = new NodeFormFilter(array(), array('query' => $query));
    if ($request->isMethod(sfRequest::POST)) {
      $this->filter->bind($request->getParameter($this->filter->getName()), $request->getFiles($this->filter->getName()));
      if ($this->filter->isValid())
      {   
        $this->setFilter($this->filter->getValues());
      }
    }
    // Build the query with the saved filters
    $query = $this->filter->buildQuery ($this->getFilter()); 
   // $query->addSelect('r.*, (select count(*) from connections where node_id = Node.id and status = \''.Connection::$TOKEN_VALIDATED.'\') as num_active_connections');  
    
    $this->nodes = $query->execute();
 /*   $this->nodes = Doctrine_Query::create()
      ->select('n.*, (select count(*) from connections where node_id = n.id and status = \''.Connection::$TOKEN_VALIDATED.'\') as num_active_connections')
      ->from('Node n')
      ->orderBy('n.name')
      ->execute(array());*/
   /* $this->nodes = Doctrine::getTable('Node')
      ->createQuery('a')
      ->execute(); */
  }

  public function executeShow(sfWebRequest $request)
  {
    $this->node = Doctrine::getTable('Node')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->node);
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new NodeForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new NodeForm();

    $this->processForm($request, $this->form);
    
    // Because we go to the edit and not the list page after creating, we need symfony to update the value of the 'id' field
    $params = $request->getParameter($this->form->getName());
    $params['id'] = $this->form->getObject()->getId();
    // And bind it again
    $this->form->bind($params);

    $this->setTemplate('edit');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($node = Doctrine::getTable('Node')->find(array($request->getParameter('id'))), sprintf('Object node does not exist (%s).', $request->getParameter('id')));
    $this->form = new NodeForm($node);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($node = Doctrine::getTable('Node')->find(array($request->getParameter('id'))), sprintf('Object node does not exist (%s).', $request->getParameter('id')));
    $this->form = new NodeForm($node);
    
    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';
      $params = $request->getParameter("submit");
      
      // If the saved button was pressed, the the submit param would be "save".  Otherwise, we notify that an unknown submit button was pressed
      $save = false;
      if (!isset($params['save'])) {
        $event = $this->dispatcher->notifyUntil(new sfEvent($this, 'nodeform.unknown_post_action', array('form' => $form, 'request' => $request)));
        $save = $event->isProcessed();
      } else {
        $save = true;
      }
      
      if ($save) {
        $node = $form->save();
        $this->getUser()->setFlash('notice', $notice);
        $this->redirect('node_edit', $node);
        
      }
      else
        $this->getUser()->setFlash('notice', "The data was not saved in the database.", false);
    } else {
      $this->getUser()->setFlash('error', "There were errors in the form", false);
    }
  }
  
 /**
  * Executes login action
  *
  * @param sfRequest $request A request object
  */
  public function executeLogin(sfWebRequest $request)
  {
    /* Get Node */
    $node = Doctrine_Query::create()
      ->from('Node')
      ->where('lower(gw_id) = ?', strtolower($request->getParameter('gw_id')))
      ->limit(1)
      ->fetchOne();

    /** @TODO: This has been patched for a quick release, but the loginpage event call
     * and the initialization of those parameters should be done more like the portal page
     * event:  not a notifyUntil but a filter whose return values is processed later in this function
     * **/
    $this->gw_id = $request->getParameter('gw_id');
    $this->gw_address = $request->getParameter('gw_address');
    $this->gw_port = $request->getParameter('gw_port');
    if (!is_null($origin_url = $request->getParameter('url')))
      $this->getUser()->setAttribute('url', $origin_url);

    $event = null;
    if (!$request->isMethod('post')) {      
      $event = $this->dispatcher->notifyUntil(new sfEvent($this, 'loginpage.request', array('node'=>$node)));
    }
    
    // If the event is not processed and there is a node or no node was specified
    if ( ($node || (!$node && (is_null($request->getParameter('gw_id'))))) && (is_null($event) || !$event->isProcessed())) { 
      
      $this->dispatcher->notify(new sfEvent($this, 'authentication.request', array('node' => $node)));

      /* Provide access to useful variables for form */
      $this->selected_authenticator = $request->getParameter('authenticator');

      /* Provide access to authenticators */
      $this->authenticators = apAuthentication::getAuthenticators();
      if (empty($this->authenticators)) {
        $this->authenticators[] = new apDefaultAuthenticator();
      }
      
      // foreach enabled authentication plugin, initiate them
      foreach ($this->authenticators as $authenticator) {
        $authenticator->setNode($node);
        $authenticator->initialize($request, $this->getIdentity());
      }
        
      /**   If this request is a POST
       *      foreach enabled authentication plugin, process the form 
       */
      if ($request->isMethod('post'))
      {
        foreach ($this->authenticators as $authenticator) {
          $authenticator->process($this, $request, $this->getIdentity());
        }
      }
      
      // If the identity is set, create the connection and call the actionVx.class.php to do what is next
      if ($this->getIdentity()->isIdentified()) {
          $this->dispatcher->notify(new sfEvent($this, 'authentication.success', array('identity' => $this->getIdentity())));
          $authmethod = $this->getIdentity()->getAuthenticatorType();
          
          // Add a privilege as logged in for the current user
          $this->getUser()->addCredential("Logged_" . $authmethod);
          $this->getUser()->addCredential("Logged");
          $this->getUser()->setAttribute('identity', $this->getIdentity());
          
          if ($node) {
             /* Create connection */ 
              $connection = new Connection();
             
              $connection->setNodeRel($node);
              $connection->setAuthType($authmethod);
              $connection->setAuthSubType($this->getIdentity()->getAuthenticatorSubType());
              $connection->setIdentity($this->getIdentity()->getId());
              $connection->setUserAgent($_SERVER['HTTP_USER_AGENT']);
              
              $event = $this->dispatcher->filter(new sfEvent($this, 'connection.pre_saved', array('connection' => $connection, 'identity' => $this->getIdentity())), $connection);
              $connection = $event->getReturnValue();
              $connection->save();
              
              $this->getIdentity()->setToken($connection->getToken());
              $this->getUser()->setAttribute('identity', $this->getIdentity());
              
              /* Forward user straight back to router */
              $this->redirect('http://' . $request->getParameter('gw_address') . ':' . $request->getParameter('gw_port') . '/wifidog/auth?token=' . $connection->getToken());
          }
          else {
              // Check if this request comes from a page that needed login to redirect to
              $referer = $this->getUser()->getAttribute('needLogin');
              // Reset the referring page
              $this->getUser()->setAttribute('needLogin', null);
              return $this->redirect( (!is_null($referer) && $referer != '') ? $referer : 'home/index');
          }
          
          return sfView::SUCCESS;
      }

      return sfView::SUCCESS;
    } elseif (!$node && (!is_null($request->getParameter('gw_id')))) {
      $this->getUser()->setFlash('notice', "The node you're trying to access does not exist on the server.", false);
      $event = $this->dispatcher->notifyUntil(new sfEvent($this, 'loginpage.node_not_found', array('request'=>$request)));
      if ($event->isProcessed())
        return sfView::SUCCESS;
      else
        return sfView::ERROR;
    } else {
      return sfView::ERROR;
    }
  }

 /**
  * Executes portal action
  *
  * @param sfRequest $request A request object
  */
  public function executePortal(sfWebRequest $request)
  {
    /* Get Node */
    $this->node = Doctrine_Query::create()
      ->from('Node')
      ->where('lower(gw_id) = ?', strtolower($request->getParameter('gw_id')))
      ->limit(1)
      ->fetchOne();

    $originurl = $this->getUser()->getAttribute('url', null);
    $default = array('template' => null, 'redirect' => $originurl);
    $event = $this->dispatcher->filter(new sfEvent($this, 'portalpage.request', array('node' => $this->node)), $default);
    $todo = $event->getReturnValue();
      
    if ($this->node) {
      
      if (!is_null($todo['template'])) {
        // A page template was set, we update the template
        $template = $todo['template'];
        if (is_string($template)) {
          $this->setTemplate($template);
        } elseif (is_array($template)) {
          $action = (isset($template['action'])?$template['action']:'portal');
          $module = (isset($template['module'])?$template['module']:'node');
          $this->setTemplate($action, $module);
        } 
      } elseif (isset($todo['redirect'])) {
        // Default template, but redirecting, so we redirect right away
        $this->redirect($todo['redirect']);
      }
      
      $this->url = $todo['redirect'];
      //$this->redirect('http://google.ca');
      return sfView::SUCCESS;
    } elseif (!$this->node && (!is_null($request->getParameter('gw_id')))) {
      $event = $this->dispatcher->notifyUntil(new sfEvent($this, 'portalpage.node_not_found', array('request'=>$request)));
      if ($event->isProcessed())
        return sfView::SUCCESS;
      else
        return sfView::ERROR;
    } else {
      return sfView::ERROR;
    }
  }

  public function executeAuth(sfWebRequest $request)
  {
    # Call the right version
    $err = error_reporting();
    error_reporting(0);
    $version = ($request->getParameter("v") ? $request->getParameter("v") : 1);
    $classname = "apActionsV" . $version;
    $this->messages = "";
    call_user_func(array($classname, 'auth'), $this, $request);
    error_reporting($err);
  }
  
  public function executeLogout(sfWebRequest $request)
  {
    # Call the right version
    $err = error_reporting();
    error_reporting(0);
    $version = ($request->getParameter("v") ? $request->getParameter("v") : 1);
    $classname = "apActionsV" . $version;
    $this->messages = "";
    call_user_func(array($classname, 'logout'), $this, $request);
    error_reporting($err);
    $this->getUser()->setAttribute('identity', null);
    $this->redirect('homepage');
  }

  public function executePing(sfWebRequest $request)
  {
    # Call the right version
    $version = ($request->getParameter("v") ? $request->getParameter("v") : 1);
    $classname = "apActionsV" . $version;
    call_user_func(array($classname, 'ping'), $this, $request);
  }
  
  public function executeGwmessage(sfWebRequest $request) {
    $message = $request->getParameter("message");
    $event = $this->dispatcher->filter(new sfEvent($this, 'node.gw_message', array('message' => $message, 'identity' => $this->getUser()->getAttribute('identity'))), array());
    $this->messages = $event->getReturnValue();
    if (empty($this->messages)) {
      if ($message == 'denied') {
        $this->messages[] = "Access denied";
      }
    }
  }
}
