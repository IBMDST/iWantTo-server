<?php
class SpeechCommentsController extends My_Center_Controller
{

	public function indexAction()
	{
		try
		{
			$method = $this->_request->getMethod();
			switch ($method)
			{
				case "GET":
					if(!$this->_request->has('id'))
					{
						$result = array();
						
						//init fields
						
						$commentsCollection = new Application_Model_DbCollections_Comments();
						try
						{
						
							$cursor = $commentsCollection->find();
							if ($cursor instanceof MongoCursor)
							{//Check the return result
								$commentsinfos = iterator_to_array($cursor);
						
								foreach ($commentsinfos as $item)
								{
						
									$temp = array('id' => $item['_id'] ->__toString(), 'userID' => $item['userID'],
											'speechID' => $item['speechID'], 'createdOn' => $item['createdOn'],
											'comment'  => $item['comment']);
									$result[] = $temp;
								}
						
								$this->returnJson(200, "Get all users comments successfully", $result);
							}
							else
							{
								$this->returnJson(200, 'no available data');
							}
						
						
						}
						catch (Zend_Exception $e)
						{
							$this->returnJson(500, 'Some errors occoured during get user feedbacks',$e->getMessage());
						}
						
						
					}
					else 
					{
						$commentsCollection = new Application_Model_DbCollections_Comments();
						$usersCollection = new Application_Model_DbCollections_Users();
						try
						{
							$id = new MongoId($this->_request->getParam('id'));
							$temp = array("speechID" => $id);
							$commentInfo = $commentsCollection->find($temp);
						
							$comments = array();
							if($commentInfo instanceof MongoCursor)
							{
								foreach($commentInfo as $comment)
								{
									$tempComment["userID"] =  $comment['userID'];
									$tempComment["speechID"] =  $comment['speechID'];
									$tempComment["createdOn"] =  $comment['createdOn']->sec;
									$tempComment["comment"] =  $comment['comment'];
									$tempComment["id"] =  $comment['_id']->__toString();
									$tempComment['userName'] =  $usersCollection->findOne(array('_id' => new MongoId($comment['userID'])))['username'];;
										
									$comments[] = $tempComment;
										
								}
							}
								
							if(is_null($commentInfo))
								$this->returnJson(200, "No comments for the speech");												
							$this->returnJson(200, "Get 1 user comment successfully",$comments);
						}
						catch (MongoException $e)
						{
							$this->returnJson(400, 'Please check the id format',$e->getMessage());
						}
						catch (Zend_Exception $e)
						{
							$this->returnJson(500, 'Some errors occoured during get one user comment',$e->getMessage());
						}
						
					}

						
					break;

				case "POST":
					if (!$this->_request->has('userID') || !$this->_request->has('speechID')
					|| !$this->_request->has('comment') )
					{
						$this->returnJson(0, 'Parameters error');
					}
					
					$userID = $this->_request->getParam('userID');
					$speechID = $this->_request->getParam('speechID');;
					$comment = $this->_request->getParam('comment');
					
					
					
					$commentsCollection = new Application_Model_DbCollections_Comments();
					$speechCollection = new Application_Model_DbCollections_Speeches();
					$userCollection = new Application_Model_DbCollections_Users();
					
					try
					{
						$id = new MongoId($speechID);
						$temp = array("_id" => $id);
						$speechInfo = $speechCollection->findOne($temp);
						if(is_null($speechInfo))
							$this->returnJson(400, "The speech did not exist");
					
						$id = new MongoId($userID);
						$temp = array("_id" => $id);
						$userInfo = $userCollection->findOne($temp);
						if(is_null($userInfo))
							$this->returnJson(400, "The user did not exist");
					
					
						$newComment = array('userID' => $userID , 'speechID' => $speechID,
								'comment' => $comment, 'createdOn' => new MongoDate(time()));
					
						$result = $commentsCollection->insert($newComment);
					
						if(!$result)
						{
							$this->returnJson(500, 'Insert action failed');
						}
					
						$this->returnJson(200, 'Insert Successfully',$result);
					
					}
					catch (MongoException $e)
					{
						$this->returnJson(400, 'Please check the id format'.$e->getMessage());
					}
					catch (Zend_Exception $e)
					{
						$this->returnJson(500, 'Some errors occoured during create user feedback'.$e->getMessage());
					}
					

					break;
				case "PUT":
					break;
				case "DELETE":
					if (!$this->_request->has('id'))
					{
						$this->returnJson(400, 'Parameters error');
					}
					
					$commentsCollection = new Application_Model_DbCollections_Comments();
					
					try
					{
						$id = new MongoId($this->_request->getParam('id'));
						$temp = array("_id" => $id);
						$feedbackInfo = $commentsCollection->findOne($temp);
					
						if(is_null($feedbackInfo))
							$this->returnJson(400, "The comment did not exist");
					
						$result = $commentsCollection->remove($temp);
					
						if (!$result)
						{
							$this->returnJson(500, "Delete action failed");
						}
					
						$this->returnJson(200, "Delete successfully",$result);
					
					}
					catch (MongoException $e)
					{
						$this->returnJson(400, 'Please check the id format',$e->getMessage());
					}
					catch (Zend_Exception $e)
					{
						$this->returnJson(500, 'Some errors occoured during delete user comment',$e->getMessage());
					}
					
					break;
				default:
					$this->returnJson(400, 'error request method');
			}
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Sorry System has some issues',$e->getMessage());
		}

	}


}