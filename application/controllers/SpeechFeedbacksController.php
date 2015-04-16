<?php
class SpeechFeedbacksController extends My_Center_Controller
{

	public function indexAction()
	{
		try
		{
			$method = $this->_request->getMethod();
			switch ($method)
			{
				case "GET":
					if($this->_request->has('userID'))
					{
						$feedbacksCollection = new Application_Model_DbCollections_Feedbacks();
						
						try
						{
							$id = $this->_request->getParam('userID');
							$temp = array("userID" => $id);
							$feedbackInfo = $feedbacksCollection->find($temp);
						
							if(is_null($feedbackInfo))
								$this->returnJson(400, "The user did not exist");
						
							$feedbacks = array();
							if($feedbackInfo instanceof MongoCursor)
							{
								foreach ($feedbackInfo as $feedback)
								{
									$tempFeedback["userID"] =  $feedback['userID'];
									$tempFeedback["stars"] =  $feedback['star'];
									$tempFeedback["speechID"] =  $feedback['speechID'];
									$tempFeedback["createdOn"] =  $feedback['createdOn']->sec;
									$tempFeedback["comment"] =  $feedback['comment'];
									$tempFeedback["id"] =  $feedback['_id']->__toString();
									
									$feedbacks[] = $tempFeedback;
								}
							}
						
							$this->returnJson(200, "Get interests successfully",$feedbacks);
						
						}
						catch (MongoException $e)
						{
							$this->returnJson(400, 'Please check the userid format'.$e->getMessage());
						}
						catch (Zend_Exception $e)
						{
							$this->returnJson(500, 'Some errors occoured during get one user interest'.$e->getMessage());
						}
						
					}
					else if($this->_request->has('id'))
					{
					
						$feedbacksCollection = new Application_Model_DbCollections_Feedbacks();
						$usersCollection = new Application_Model_DbCollections_Users();
						try
						{
							$id = new MongoId($this->_request->getParam('id'));
							$temp = array("speechID" => $id);
							$feedbacksInfo = $feedbacksCollection->find($temp);
							
							$feedbacks = array();
							if($feedbacksInfo instanceof MongoCursor)
							{
								foreach ($feedbacksInfo as $feedback)
								{
									$tempFeedback["userID"] =  $feedback['userID'];
									$tempFeedback["stars"] =  $feedback['star'];
									$tempFeedback["speechID"] =  $feedback['speechID'];
									$tempFeedback["createdOn"] =  $feedback['createdOn']->sec;
									$tempFeedback["comment"] =  $feedback['comment'];
									$tempFeedback["id"] =  $feedback['_id']->__toString();
									$tempFeedback['userName'] =  $usersCollection->findOne(array('_id' => new MongoId($feedback['userID'])))['username'];;
							
									$feedbacks[] = $tempFeedback;
								}
							}

							if(is_null($feedbacksInfo))
								$this->returnJson(200, "The feedback did not exist");
				
							$this->returnJson(200, "Get 1 user feedback successfully",$feedbacks);
						
						}
						catch (MongoException $e)
						{
							$this->returnJson(400, 'Please check the id format'.$e->getMessage());
						}
						catch (Zend_Exception $e)
						{
							$this->returnJson(500, 'Some errors occoured during get one user feedback'.$e->getMessage());
						}
					}
					else
					{
						$feedbacksCollection = new Application_Model_DbCollections_Feedbacks();
						try
						{
							$cursor = $feedbacksCollection->find();
							if ($cursor instanceof MongoCursor)
							{//Check the return result
								$feedbacksinfos = iterator_to_array($cursor);
						
								foreach ($feedbacksinfos as $item)
								{
						
									$temp = array('id' => $item['_id'] ->__toString(), 'userID' => $item['userID'],
											'speechID' => $item['speechID'], 'createdOn' => $item['createdOn']->sec, 'stars' => $item['star'],
											'comment'  => $item['comment']);
									$result[] = $temp;
								}
						
								$this->returnJson(200, "Get all users feedbacks successfully", $result);
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
					break;
	
				case "POST":
					if (!$this->_request->has('userID') || !$this->_request->has('speechID')
					|| !$this->_request->has('stars') || !$this->_request->has('comment') )
					{
						$this->returnJson(400, 'Parameters error');
					}
					
					$userID = $this->_request->getParam('userID');
					$speechID = $this->_request->getParam('speechID');
					$star = $this->_request->getParam('stars');
					$comment = $this->_request->getParam('comment');
					
					if(!is_numeric($star))
					{
						$this->returnJson(400, 'Parameters error');
					}
					
					if($star > 5 || $star < 1)
					{
						$this->returnJson(400, 'Parameters error');
					}
					
					
					
					$feedbacksCollection = new Application_Model_DbCollections_Feedbacks();
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
					
					
						$temp = array("speechID" => $speechID, "userID" => $userID);
						$feedbackInfo = $feedbacksCollection->findOne($temp);
					
						if(isset($feedbackInfo)&&!empty($feedbackInfo))
							$this->returnJson(400, "The feedback has already exist");
					
						$newFeedback = array('userID' => $userID , 'speechID' => $speechID,
								'star' => $star, 'comment' => $comment, 'createdOn' => new MongoDate(time()));
					
						$result = $feedbacksCollection->insert($newFeedback);
					
						if(!$result)
						{
							$this->returnJson(500, 'Insert action failed');
						}
					
						
						$feedbackTemp = $feedbacksCollection -> findOne(array('speechID' => $speechID,'userID' => $userID));
						$feedback['id'] = $feedbackTemp['_id']->__toString();
						$feedback['userID'] = $feedbackTemp['userID'];
						$feedback['speechID'] = $feedbackTemp['speechID'];
						$feedback['createdOn'] = $feedbackTemp['createdOn']->sec;
						$feedback['comment']= $feedbackTemp['comment'];
						$feedback['star']= $feedbackTemp['star'];
						
						
						$this->returnJson(200, 'Insert Successfully',$feedback);
					
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
					if (!$this->_request->has('id') || !$this->_request->has('stars')
					|| !$this->_request->has('comment') )
					{
						$this->returnJson(400, 'Parameters error');
					}
						
						
					$id = $this->_request->getParam('id');
					$stars = $this->_request->getParam('stars');;
					$comment = $this->_request->getParam('comment');

						
						
					$feedbackCollection = new Application_Model_DbCollections_Feedbacks();
						
					try
					{
						$id = new MongoId($this->_request->getParam('id'));
						$feedbackTemp = array("_id" => $id);
							
						$feedbackInfo = $feedbackCollection->findOne($feedbackTemp);
						if(is_null($feedbackInfo))
							$this->returnJson(400, "The feedback did not exist");
							
							
						$feedbackData = array('comment' => $comment , 'star' => $stars,
								'speechID' =>  $feedbackInfo['speechID'], 'userID' => $feedbackInfo['userID'],//for test
								'createdOn' => new MongoDate(time()));
							
						$result = $feedbackCollection->update($feedbackTemp,$feedbackData);
							
						if(!$result)
						{
							$this->returnJson(500, 'Update action failed');
						}
							
						$this->returnJson(200, 'Update successfully',$result);
							
							
					}
					catch (MongoException $e)
					{
						$this->returnJson(400, 'Check the parameters format'.$e->getMessage());
					}
					catch (Zend_Exception $e)
					{
						$this->returnJson(500, 'Some errors occoured during update feedback',$e->getMessage());
					}
					break;
				case "DELETE":
					if (!$this->_request->has('id'))
					{
						$this->returnJson(400, 'Parameters error');
					}
					
					$feedbacksCollection = new Application_Model_DbCollections_Feedbacks();
					
					try
					{
						$id = new MongoId($this->_request->getParam('id'));
						$temp = array("_id" => $id);
						$feedbackInfo = $feedbacksCollection->findOne($temp);
					
						if(is_null($feedbackInfo))
							$this->returnJson(400, "The feedback did not exist");
					
						$result = $feedbacksCollection->remove($temp);
					
						if (!$result)
						{
							$this->returnJson(400, "Delete action failed");
						}
					
						$this->returnJson(200, "Delete successfully",$result);
					
					}
					catch (MongoException $e)
					{
						$this->returnJson(400, 'Please check the id format'.$e->getMessage());
					}
					catch (Zend_Exception $e)
					{
						$this->returnJson(500, 'Some errors occoured during delete user feedback'.$e->getMessage());
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