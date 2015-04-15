<?php
class SpeechesController extends My_Center_Controller
{
	
	public function indexAction()
	{
		try
		{
			$method = $this->_request->getMethod();
			switch ($method)
			{
				case "GET":
					if (!$this->_request->has('id'))
					{
						$result = array();
						
						//init fields
						
						$speechesCollection = new Application_Model_DbCollections_Speeches();
						$commentsCollection = new Application_Model_DbCollections_Comments();
						$interestsCollection = new Application_Model_DbCollections_Interests();
						$feedbacksCollection = new Application_Model_DbCollections_Feedbacks();
						$usersCollection = new Application_Model_DbCollections_Users();
						try
						{
						
							$cursor = $speechesCollection->find();
							if ($cursor instanceof MongoCursor)
							{//Check the return result
								$speechessinfos = iterator_to_array($cursor);
						
								foreach ($speechessinfos as $item)
								{
									$commentsCursor =null;
									$comments = array();
									$commentsCursor = $commentsCollection -> find(array('speechID' => $item['_id'] ->__toString()));
									if($commentsCursor instanceof MongoCursor)
									{
										foreach($commentsCursor as $comment)
										{
											$temp["userID"] =  $comment['userID'];
											$temp["speechID"] =  $comment['speechID'];
											$temp["createdOn"] =  $comment['createdOn']->sec;
											$temp["comment"] =  $comment['comment'];
											$temp["id"] =  $comment['_id']->__toString();
											$temp['userName'] =  $usersCollection->findOne(array('_id' => new MongoId($comment['userID'])))['username'];;
											
											$comments[] = $temp;
											
										}
									}
									
										
									$interestsCursor = null;
									$interestsCursor = $interestsCollection -> find(array('speechID' => $item['_id'] ->__toString()),array('_id' => 0,'speechID'=>0));
									if($interestsCursor instanceof MongoCursor)
									{
										foreach($interestsCursor as $interest)
										{
											$temp["userID"] =  $interest['userID'];
											$temp["speechID"] =  $interest['speechID'];
											$temp["createdOn"] =  $interest['createdOn']->sec;
											$temp["id"] =  $interest['_id']->__toString();
											$temp['userName'] =  $usersCollection->findOne(array('_id' => new MongoId($interest['userID'])))['username'];;
											
											$interests[] = $temp;
											
										}
									}
									
									$feedbacksCursor = null;
									$feedbacks = array();
									$feedbacksCursor = $feedbacksCollection -> find(array('speechID' => $item['_id'] ->__toString()));
									if($feedbacksCursor instanceof MongoCursor)
									{
										foreach ($feedbacksCursor as $feedback)
										{			
											$temp["userID"] =  $feedback['userID'];
											$temp["stars"] =  $feedback['star'];
											$temp["speechID"] =  $feedback['speechID'];
											$temp["createdOn"] =  $feedback['createdOn']->sec;
											$temp["comment"] =  $feedback['comment'];
											$temp["id"] =  $feedback['_id']->__toString();
											$temp['userName'] =  $usersCollection->findOne(array('_id' => new MongoId($feedback['userID'])))['username'];;
											 
											$feedbacks[] = $temp;
										}
									}
									
									$userInfo = $usersCollection->findOne(array('_id' => new MongoId($item['speakerID'])));
		
										
									$temp = array('id' => $item['_id'] ->__toString(), 'speakerID' => $item['speakerID'],
											'subject' => $item['subject'],'description'=>$item['description'],'when'=>$item['when'],
											'where' => $item['where'],
											'createdOn' => $item['createdOn']->sec,
											'comments'  => $comments,'interests' => $interests, 'feedbacks'=>$feedbacks,'fixed' => $item['fixed'],
											'speakerName' => $userInfo['username']);
									$result[] = $temp;
								}
						
								$this->returnJson(200, "Get all speeches successfully", $result);
							}
							else
							{
								$this->returnJson(200, 'no available data');
							}
						
						
						}
						catch (Zend_Exception $e)
						{
							$this->returnJson(500, 'Some errors occoured during get all speeches',$e->getMessage());
						}
					}
					else 
					{
						$speechesCollection = new Application_Model_DbCollections_Speeches();
						$commentsCollection = new Application_Model_DbCollections_Comments();
						$interestsCollection = new Application_Model_DbCollections_Interests();
						$feedbacksCollection = new Application_Model_DbCollections_Feedbacks();
						$usersCollection = new Application_Model_DbCollections_Users();
						
						try
						{
							$id = new MongoId($this->_request->getParam('id'));
							$temp = array("_id" => $id);
							$speechInfo = $speechesCollection->findOne($temp);
						
							if(is_null($speechInfo))
								$this->returnJson(0, "The speech did not exist");
								
							$commentsCursor =null;
							$commentsCursor = $commentsCollection -> find(array('speechID' => $speechInfo['_id'] ->__toString()),array('_id' => 0,'speechID' => 0));
							if($commentsCursor instanceof MongoCursor)
							{
								foreach($commentsCursor as $comment)
								{
									$temp["userID"] =  $comment['userID'];
									$temp["speechID"] =  $comment['speechID'];
									$temp["createdOn"] =  $comment['createdOn']->sec;
									$temp["comment"] =  $comment['comment'];
									$temp["id"] =  $comment['_id']->__toString();
									$temp['userName'] =  $usersCollection->findOne(array('_id' => new MongoId($comment['userID'])))['username'];;
										
									$comments[] = $temp;
										
								}
							}
								
							$interestsCursor = null;
							$interestsCursor = $interestsCollection -> find(array('speechID' => $speechInfo['_id'] ->__toString()),array('_id' => 0,'speechID'=>0));
							if($interestsCursor instanceof MongoCursor)
							{
								foreach($interestsCursor as $interest)
								{
									$temp["userID"] =  $interest['userID'];
									$temp["speechID"] =  $interest['speechID'];
									$temp["createdOn"] =  $interest['createdOn']->sec;
									$temp["id"] =  $interest['_id']->__toString();
									$temp['userName'] =  $usersCollection->findOne(array('_id' => new MongoId($interest['userID'])))['username'];;
										
									$interests[] = $temp;
										
								}
							}
							
							$feedbacksCursor = null;
							$feedbacksCursor = $interestsCollection -> find(array('speechID' => $speechInfo['_id'] ->__toString()),array('_id' => 0,'speechID'=>0));
							if($feedbacksCursor instanceof MongoCursor)
							{
								foreach ($feedbacksCursor as $feedback)
								{
									$temp["userID"] =  $feedback['userID'];
									$temp["stars"] =  $feedback['star'];
									$temp["speechID"] =  $feedback['speechID'];
									$temp["createdOn"] =  $feedback['createdOn']->sec;
									$temp["comment"] =  $feedback['comment'];
									$temp["id"] =  $feedback['_id']->__toString();
									$temp['userName'] =  $usersCollection->findOne(array('_id' => new MongoId($feedback['userID'])))['username'];;
								
									$feedbacks[] = $temp;
								}
							}
								
							$userInfo = $usersCollection->findOne(array('_id' => new MongoId($speechInfo['speakerID'])));
						
								
							$result = array('id' => $speechInfo['_id'] ->__toString(), 'speakerID' => $speechInfo['speakerID'],
											'subject' => $speechInfo['subject'],'description'=>$speechInfo['description'],'when'=>$speechInfo['when'],
											'where' => $speechInfo['where'],
											'createdOn' => $speechInfo['createdOn']->sec,
											'comment'  => $comments,'interests' => $interests, 'feedbacks'=>$feedbacks ,'fixed' => $speechInfo['fixed'],
											'speakerName' => $userInfo['username']);
						

							$this->returnJson(200, "Get 1 speech successfully", $result);
								
						}
						catch (MongoException $e)
						{
							$this->returnJson(400, 'Please check the id format'.$e->getMessage());
						}
						catch (Zend_Exception $e)
						{
							$this->returnJson(500, 'Some errors occoured during get 1 speech'.$e->getMessage());
						}
						
					}
					break;
						
				case "POST":
					if (!$this->_request->has('speakerID') || !$this->_request->has('subject')
					|| !$this->_request->has('description') )
					{
						$this->returnJson(400, 'Parameters error');
					}
					
					$speakerID = $this->_request->getParam('speakerID');
					$subject = $this->_request->getParam('subject');;
					$description = $this->_request->getParam('description');
					$when = ($this->_request->has('when'))?$this->_request->getParam('when'):null;
					$where = ($this->_request->has('where'))?$this->_request->getParam('where'):null;
					$fixed = ($this->_request->has('fixed'))?$this->_request->getParam('fiexed'):false;
					
					
					
					$speechCollection = new Application_Model_DbCollections_Speeches();
					$userCollection = new Application_Model_DbCollections_Users();
					
					try
					{
							
						$speechInfo = $speechCollection->findOne(array('speakerID' => $speakerID, 'subject' => $subject, 'description' => $description));
						if(!is_null($speechInfo))
							$this->returnJson(400, "The speech has already exist");
					
						$id = new MongoId($speakerID);
						$temp = array("_id" => $id);
						$userInfo = $userCollection->findOne($temp);
						if(is_null($userInfo))
							$this->returnJson(400, "The speaker did not exist");
					
					
						$newSpeech = array('subject' => $subject , 'description' => $description,
								'speakerID' => $speakerID, 'when' => $when,//for test
								'where' => $where,'fixed' => $fixed,'createdOn' => new MongoDate(time()));
					
						$result = $speechCollection->insert($newSpeech);
					
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
						$this->returnJson(500, 'Some errors occoured during create speech'.$e->getMessage());
					}
					
					break;
				case "PUT":

					if (!$this->_request->has('id') || !$this->_request->has('subject')
					|| !$this->_request->has('description') )
					{
						$this->returnJson(400, 'Parameters error');
					}
					
					
					$id = $this->_request->getParam('id');
					$subject = $this->_request->getParam('subject');;
					$description = $this->_request->getParam('description');
					$when = ($this->_request->has('when'))?$this->_request->getParam('when'):null;
					$where = ($this->_request->has('where'))?$this->_request->getParam('where'):null;
					$fixed = ($this->_request->has('fixed'))?$this->_request->getParam('fiexed'):false;
					
					
					$speechCollection = new Application_Model_DbCollections_Speeches();
					$userCollection = new Application_Model_DbCollections_Users();
					
					try
					{
						$id = new MongoId($this->_request->getParam('id'));
						$speechTemp = array("_id" => $id);
							
						$speechInfo = $speechCollection->findOne($speechTemp);
						if(is_null($speechInfo))
							$this->returnJson(400, "The speech did not exist");
					
					
						$speechData = array('subject' => $subject , 'description' => $description,
								'speakerID' =>  $speechInfo['speakerID'], 'when' => $when,//for test
								'where' => $where,'fixed' => $fixed,'createdOn' => new MongoDate(time()));
					
						$result = $speechCollection->update($speechTemp,$speechData);
					
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
						$this->returnJson(500, 'Some errors occoured during update speech',$e->getMessage());
					}
					break;
				case "DELETE":
					if (!$this->_request->has('id'))
					{
						$this->returnJson(400, 'Parameters error');
					}
					
					$speechesCollection = new Application_Model_DbCollections_Speeches();
					$commentsCollection = new Application_Model_DbCollections_Comments();
					$interestsCollection = new Application_Model_DbCollections_Interests();
					$feedbackCollection = new Application_Model_DbCollections_Feedbacks();
					
					try
					{
						$id = new MongoId($this->_request->getParam('id'));
						$temp = array("_id" => $id);
						$speechesInfo = $speechesCollection->findOne($temp);
					
						if(is_null($speechesInfo))
							$this->returnJson(400, "The speech did not exist");
							
						//transaction process?
						$speechResult = $speechesCollection->remove($temp);
						$commentResult = $commentsCollection->remove(array('speechID' => $speechesInfo['_id']->__toString()));
						$feedbackResult = $feedbackCollection->remove(array('speechID' => $speechesInfo['_id']->__toString()));
						$interestResult = $interestsCollection->remove(array('speechID' => $speechesInfo['_id']->__toString()));
							
							
					
						if (!$speechResult || !$commentResult || !$feedbackResult || !$interestResult)
						{
							$this->returnJson(500, "Delete action failed");
						}
					
						$this->returnJson(200, "Delete successfully",true);
					
					}
					catch (MongoException $e)
					{
						$this->returnJson(400, 'Please check the id format'.$e->getMessage());
					}
					catch (Zend_Exception $e)
					{
						$this->returnJson(500, 'Some errors occoured during delete user comment'.$e->getMessage());
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
	
	
	
	/**
	 * Author: Shaon
	 * 
	 * List all interests
	 * path: /user/get-all-interests
	 * Result: {"id":"550fc0ac11be313074a8399e","userID":"550ba28c0cde3a0305c027a1","speechID":"550ba28c0cde3a0305c027a1","createdOn":{"sec":1427095724,"usec":694000}}
	 */
	public function getAllInterestsAction()
	{

		$result = array();
		
		//init fields
		
		$interestsCollection = new Application_Model_DbCollections_Interests();
		try 
		{
			
			
			
			$cursor = $interestsCollection->find();
			if ($cursor instanceof MongoCursor)
			{//Check the return result
				$interestinfos = iterator_to_array($cursor);
				
				foreach ($interestinfos as $item)
				{				
					
 					$temp = array('id' => $item['_id'] ->__toString(), 'userID' => $item['userID'],
						'speechID' => $item['speechID'], 'createdOn' => $item['createdOn']);
 					$result[] = $temp;
				}
		
				$this->returnJson(1, "Get all users interests successfully", $result);
			}
			else 
			{
				$this->returnJson(0, 'no available data');
			}
				
			
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during get user interests',$e->getMessage());
		}

	}
	
	
	/**
	 * Author: Shaon
	 *
	 * List one interest
	 * path: /user/get-one-interest
	 * Result: {"id":"550fc0ac11be313074a8399e","userID":"550ba28c0cde3a0305c027a1","speechID":"550ba28c0cde3a0305c027a1","createdOn":{"sec":1427095724,"usec":694000}}
	 */
	public function getOneInterestAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		if (!$this->_request->has('id'))
		{
			$this->returnJson(0, 'Parameters error');
		}
		
		$interestsCollection = new Application_Model_DbCollections_Interests();
		
		try 
		{
			$id = new MongoId($this->_request->getParam('id'));
			$temp = array("_id" => $id);
			$interestInfo = $interestsCollection->findOne($temp);
			
			

				
			if(is_null($interestInfo))
				$this->returnJson(0, "The user did not exist");
			
			$result = array('id' => $interestInfo['_id'] ->__toString(), 'userID' => $interestInfo['userID'],
						'speechID' => $interestInfo['speechID'], 'createdOn' => $interestInfo['createdOn']);
			
			
			$this->returnJson(1, "Get 1 user interest successfully",$result);
			
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Please check the id format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during get one user interest',$e->getMessage());
		}
		
	}
	
	
	
	
	/**
	 * Author: Shaon
	 * Create a interest via userID, speechID
	 * Path: speeches/create-interest
	 * Result: return status 1 if insert succefully.
	 * 
	 */
	public function createInterestAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		
		if (!$this->_request->has('userID') || !$this->_request->has('speechID'))
		{
			$this->returnJson(0, 'Parameters error');
		}
		
		$userID = $this->_request->getParam('userID');
		$speechID = $this->_request->getParam('speechID');
		
		
		$interestsCollection = new Application_Model_DbCollections_Interests();
		$speechCollection = new Application_Model_DbCollections_Speeches();
		$userCollection = new Application_Model_DbCollections_Users();
		
		try 
		{
			$id = new MongoId($speechID);
			$temp = array("_id" => $id);
			$speechInfo = $speechCollection->findOne($temp);
			if(is_null($speechInfo))
				$this->returnJson(0, "The speech did not exist");
			
			$id = new MongoId($userID);
			$temp = array("_id" => $id);
			$userInfo = $userCollection->findOne($temp);
			if(is_null($userInfo))
				$this->returnJson(0, "The user did not exist");
			
			
			$temp = array("speechID" => $speechID, "userID" => $userID);
			$interestInfo = $interestsCollection->findOne($temp);
				
			if(isset($interestInfo)&&!empty($interestInfo))
				$this->returnJson(0, "The interest has already exist");
			
			$newInterest = array('userID' => $userID , 'speechID' => $speechID, 'createdOn' => new MongoDate(time()));
			
			$result = $interestsCollection->insert($newInterest);
			
			if(!$result)
			{
				$this->returnJson(0, 'Insert action failed');
			}
			
			$this->returnJson(1, 'Insert Successfully',$result);

		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Please check the id format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during create user profile',$e->getMessage());
		}
		
	
	
	}
	
	
	/**
	 * Author: Shaon
	 * Delete a interest
	 * Path : users/del-interest
	 * Result: return status 1 if delete succefully.
	 * 
	 */
	public function delInterestAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		
		if (!$this->_request->has('id'))
		{
			$this->returnJson(0, 'Parameters error');
		}
		
		$interestsCollection = new Application_Model_DbCollections_Interests();
		
		try 
		{
			$id = new MongoId($this->_request->getParam('id'));
			$temp = array("_id" => $id);
			$interestInfo = $interestsCollection->findOne($temp);
				
			if(is_null($interestInfo))
				$this->returnJson(0, "The interest did not exist");
			
			$result = $interestsCollection->remove($temp);
			
			if (!$result)
			{
				$this->returnJson(0, "Delete action failed");
			}
			
			$this->returnJson(1, "Delete successfully",$result);
			
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Please check the id format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during delete user profile',$e->getMessage());
		}
	
		
	}
	
	
	/**
	 * Author: Shaon
	 *
	 * List all feedbacks
	 * path: /user/get-all-feedbacks
	 * Result: {"id":"550fd6b811be313074a8399f","userID":"550ba28c0cde3a0305c027a1","speechID":"550faef111be313074a8399c","createdOn":{"sec":1427101368,"usec":927000},"stars":5,"comment":"very good"}
	 */
	
	public function getAllFeedbacksAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		$result = array();
	
		//init fields
	
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
							'speechID' => $item['speechID'], 'createdOn' => $item['createdOn'], 'stars' => $item['stars'],
							'comment'  => $item['comment']);
					$result[] = $temp;
				}
	
				$this->returnJson(1, "Get all users feedbacks successfully", $result);
			}
			else
			{
				$this->returnJson(0, 'no available data');
			}
	
				
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during get user feedbacks',$e->getMessage());
		}
	
	}
	
	
	/**
	 * Author: Shaon
	 *
	 * List one feedback
	 * path: /user/get-one-feedback
	 * Result: {"id":"550fd6b811be313074a8399f","userID":"550ba28c0cde3a0305c027a1","speechID":"550faef111be313074a8399c","createdOn":{"sec":1427101368,"usec":927000},"stars":5,"comment":"very good"}
	 */
	public function getOneFeedbackAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		if (!$this->_request->has('id'))
		{
			$this->returnJson(0, 'Parameters error');
		}
	
		$feedbacksCollection = new Application_Model_DbCollections_Feedbacks();
	
		try
		{
			$id = new MongoId($this->_request->getParam('id'));
			$temp = array("_id" => $id);
			$feedbackInfo = $feedbacksCollection->findOne($temp);
				
				
	
	
			if(is_null($feedbackInfo))
				$this->returnJson(0, "The feedback did not exist");
				
			$result = array('id' => $feedbackInfo['_id'] ->__toString(), 'userID' => $feedbackInfo['userID'],
							'speechID' => $feedbackInfo['speechID'], 'createdOn' => $feedbackInfo['createdOn'], 'stars' => $feedbackInfo['stars'],
							'comment'  => $feedbackInfo['comment']);
				
				
			$this->returnJson(1, "Get 1 user feedback successfully",$result);
				
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Please check the id format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during get one user feedback',$e->getMessage());
		}
	
	}
	
	
	
	
	/**
	 * Author: Shaon
	 * Create a feedback via userID, speechID, star, comment
	 * Path: speeches/create-feedback
	 * Result: return status 1 if insert successfully.
	 *
	 */
	public function createFeedbackAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
	
		if (!$this->_request->has('userID') || !$this->_request->has('speechID')
			|| !$this->_request->has('star') || !$this->_request->has('comment') )
		{
			$this->returnJson(0, 'Parameters error');
		}
	
		$userID = $this->_request->getParam('userID');
		$speechID = $this->_request->getParam('speechID');
		$star = $this->_request->getParam('star');
		$comment = $this->_request->getParam('comment');
		
		if(!is_numeric($star))
		{
			$this->returnJson(0, 'Parameters error');
		}
		
		if($star > 5 || $star < 1)
		{
			$this->returnJson(0, 'Parameters error');
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
				$this->returnJson(0, "The speech did not exist");
				
			$id = new MongoId($userID);
			$temp = array("_id" => $id);
			$userInfo = $userCollection->findOne($temp);
			if(is_null($userInfo))
				$this->returnJson(0, "The user did not exist");
				
				
			$temp = array("speechID" => $speechID, "userID" => $userID);
			$feedbackInfo = $feedbacksCollection->findOne($temp);
	
			if(isset($feedbackInfo)&&!empty($feedbackInfo))
				$this->returnJson(0, "The feedback has already exist");
				
			$newFeedback = array('userID' => $userID , 'speechID' => $speechID, 
					'star' => $star, 'comment' => $comment, 'createdOn' => new MongoDate(time()));
				
			$result = $feedbacksCollection->insert($newFeedback);
				
			if(!$result)
			{
				$this->returnJson(0, 'Insert action failed');
			}
				
			$this->returnJson(1, 'Insert Successfully',$result);
	
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Please check the id format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during create user feedback',$e->getMessage());
		}
	
	
	
	}
	
	
	/**
	 * Author: Shaon
	 * Delete a feedback
	 * Path : users/del-feedback
	 * Result: return status 1 if delete successfully.
	 *
	 */
	public function delFeedbackAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
	
		if (!$this->_request->has('id'))
		{
			$this->returnJson(0, 'Parameters error');
		}
	
		$feedbacksCollection = new Application_Model_DbCollections_Feedbacks();
	
		try
		{
			$id = new MongoId($this->_request->getParam('id'));
			$temp = array("_id" => $id);
			$feedbackInfo = $feedbacksCollection->findOne($temp);
	
			if(is_null($feedbackInfo))
				$this->returnJson(0, "The interest did not exist");
				
			$result = $feedbacksCollection->remove($temp);
				
			if (!$result)
			{
				$this->returnJson(0, "Delete action failed");
			}
				
			$this->returnJson(1, "Delete successfully",$result);
				
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Please check the id format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during delete user feedback',$e->getMessage());
		}
	
	
	}
	
	
	/**
	 * Author: Shaon
	 *
	 * List all comments
	 * path: /user/get-all-comments
	 * Result: {"id":"550fd6b811be313074a8399f","userID":"550ba28c0cde3a0305c027a1","speechID":"550faef111be313074a8399c","createdOn":{"sec":1427101368,"usec":927000},"stars":5,"comment":"very good"}
	 */
	
	public function getAllCommentsAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
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
	
				$this->returnJson(1, "Get all users comments successfully", $result);
			}
			else
			{
				$this->returnJson(0, 'no available data');
			}
	
	
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during get user feedbacks',$e->getMessage());
		}
	
	}
	
	
	/**
	 * Author: Shaon
	 *
	 * List one comment
	 * path: /user/get-one-comment
	 * Result: {"id":"550fd6b811be313074a8399f","userID":"550ba28c0cde3a0305c027a1","speechID":"550faef111be313074a8399c","createdOn":{"sec":1427101368,"usec":927000},"stars":5,"comment":"very good"}
	 */
	public function getOneCommentAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		if (!$this->_request->has('id'))
		{
			$this->returnJson(0, 'Parameters error');
		}
	
		$commentsCollection = new Application_Model_DbCollections_Comments();
	
		try
		{
			$id = new MongoId($this->_request->getParam('id'));
			$temp = array("_id" => $id);
			$commentInfo = $commentsCollection->findOne($temp);
	
	
	
	
			if(is_null($commentInfo))
				$this->returnJson(0, "The feedback did not exist");
	
			$result = array('id' => $commentInfo['_id'] ->__toString(), 'userID' => $commentInfo['userID'],
					'speechID' => $commentInfo['speechID'], 'createdOn' => $commentInfo['createdOn'],
					'comment'  => $commentInfo['comment']);
	
	
			$this->returnJson(1, "Get 1 user comment successfully",$result);
	
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Please check the id format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during get one user comment',$e->getMessage());
		}
	
	}
	
	
	
	
	/**
	 * Author: Shaon
	 * Create a comment via userID, speechID, comment
	 * Path: speeches/create-comment
	 * Result: return status 1 if insert successfully.
	 *
	 */
	public function createCommentAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
	
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
				$this->returnJson(0, "The speech did not exist");
	
			$id = new MongoId($userID);
			$temp = array("_id" => $id);
			$userInfo = $userCollection->findOne($temp);
			if(is_null($userInfo))
				$this->returnJson(0, "The user did not exist");
	

			$newComment = array('userID' => $userID , 'speechID' => $speechID,
					 'comment' => $comment, 'createdOn' => new MongoDate(time()));
	
			$result = $commentsCollection->insert($newComment);
	
			if(!$result)
			{
				$this->returnJson(0, 'Insert action failed');
			}
	
			$this->returnJson(1, 'Insert Successfully',$result);
	
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Please check the id format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during create user feedback',$e->getMessage());
		}
	
	
	
	}
	
	
	/**
	 * Author: Shaon
	 * Delete a comment
	 * Path : users/del-comment
	 * Result: return status 1 if delete succefully.
	 *
	 */
	public function delCommentAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
	
		if (!$this->_request->has('id'))
		{
			$this->returnJson(0, 'Parameters error');
		}
	
		$commentsCollection = new Application_Model_DbCollections_Comments();
	
		try
		{
			$id = new MongoId($this->_request->getParam('id'));
			$temp = array("_id" => $id);
			$feedbackInfo = $commentsCollection->findOne($temp);
	
			if(is_null($feedbackInfo))
				$this->returnJson(0, "The comment did not exist");
	
			$result = $commentsCollection->remove($temp);
	
			if (!$result)
			{
				$this->returnJson(0, "Delete action failed");
			}
	
			$this->returnJson(1, "Delete successfully",$result);
	
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Please check the id format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during delete user comment',$e->getMessage());
		}
	
	
	}
	
	/**
	 * Author: Shaon
	 *
	 * List all speeches
	 * path: user/get-all-speeches
	 * Result: {"id":"550fd6b811be313074a8399f","userID":"550ba28c0cde3a0305c027a1","speechID":"550faef111be313074a8399c","createdOn":{"sec":1427101368,"usec":927000},"stars":5,"comment":"very good"}
	 */
	public function getAllSpeechesAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		$result = array();
	
		//init fields
	
		$speechesCollection = new Application_Model_DbCollections_Speeches();
		$commentsCollection = new Application_Model_DbCollections_Comments();
		$interestsCollection = new Application_Model_DbCollections_Interests();
		
		try
		{
	
			$cursor = $speechesCollection->find();
			if ($cursor instanceof MongoCursor)
			{//Check the return result
				$speechessinfos = iterator_to_array($cursor);
	
				foreach ($speechessinfos as $item)
				{
					$commentsCursor =null;
					$commentsCursor = $commentsCollection -> find(array('speechID' => $item['_id'] ->__toString()),array('_id' => 0,'speechID' => 0));
					if($commentsCursor instanceof MongoCursor)
						$commentsInfo = iterator_to_array($commentsCursor);
					
					$interestsCursor = null;
					$interestsCursor = $interestsCollection -> find(array('speechID' => $item['_id'] ->__toString()),array('_id' => 0,'speechID'=>0));
					if($interestsCursor instanceof MongoCursor)
					{			
						$interestsInfo = iterator_to_array($interestsCursor);
					}
					
					$temp = array('id' => $item['_id'] ->__toString(), 'speakerID' => $item['speakerID'],
							'subject' => $item['subject'],'description'=>$item['description'],
							 'createdOn' => $item['createdOn'],
							'comment'  => $commentsInfo,'interest' => $interestsInfo, 'fixed' => $item['fixed']);
					$result[] = $temp;
				}
	
				$this->returnJson(1, "Get all speeches successfully", $result);
			}
			else
			{
				$this->returnJson(0, 'no available data');
			}
	
	
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during get all speeches',$e->getMessage());
		}
	
	}
	
	
	/**
	 * Author: Shaon
	 *
	 * List one speech
	 * path: /user/get-one-speech
	 * Result: {"id":"550fd6b811be313074a8399f","userID":"550ba28c0cde3a0305c027a1","speechID":"550faef111be313074a8399c","createdOn":{"sec":1427101368,"usec":927000},"stars":5,"comment":"very good"}
	 */
	public function getOneSpeechAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
		
		if (!$this->_request->has('id'))
		{
			$this->returnJson(0, 'Parameters error');
		}
		
	
		//init fields
	
		$speechesCollection = new Application_Model_DbCollections_Speeches();
		$commentsCollection = new Application_Model_DbCollections_Comments();
		$interestsCollection = new Application_Model_DbCollections_Interests();
		
		try
		{
			$id = new MongoId($this->_request->getParam('id'));
			$temp = array("_id" => $id);
			$speechInfo = $speechesCollection->findOne($temp);

			if(is_null($speechInfo))
				$this->returnJson(0, "The speech did not exist");
			
				$commentsCursor =null;
				$commentsCursor = $commentsCollection -> find(array('speechID' => $speechInfo['_id'] ->__toString()),array('_id' => 0,'speechID' => 0));
				if($commentsCursor instanceof MongoCursor)
					$commentsInfo = iterator_to_array($commentsCursor);
					
				$interestsCursor = null;
				$interestsCursor = $interestsCollection -> find(array('speechID' => $speechInfo['_id'] ->__toString()),array('_id' => 0,'speechID'=>0));
				if($interestsCursor instanceof MongoCursor)
					$interestsInfo = iterator_to_array($interestsCursor);
				
					
				$result = array('id' => $speechInfo['_id'] ->__toString(), 'speakerID' => $speechInfo['speakerID'],
							'subject' => $speechInfo['subject'],'description'=>$speechInfo['description'],
							 'createdOn' => $speechInfo['createdOn'],
							'comment'  => $commentsInfo,'interest' => $interestsInfo, 'fixed' => $speechInfo['fixed']);
				
				

				$this->returnJson(1, "Get 1 speech successfully", $result);
			

	
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Please check the id format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during get 1 speech',$e->getMessage());
		}
	
	}
	
	
	
	
	/**
	 * Author: Shaon
	 * Create a speech via subject, description, speakerID,when,where,fixed,createdOn
	 * Path: speeches/create-speech
	 * Result: return status 1 if insert succefully.
	 *
	 */
	public function createSpeechAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
	
		if (!$this->_request->has('speakerID') || !$this->_request->has('subject')
		|| !$this->_request->has('description') )
		{
			$this->returnJson(0, 'Parameters error');
		}
	
		$speakerID = $this->_request->getParam('speakerID');
		$subject = $this->_request->getParam('subject');;
		$description = $this->_request->getParam('description');
		$when = ($this->_request->has('when'))?$this->_request->getParam('when'):null;
		$where = ($this->_request->has('where'))?$this->_request->getParam('where'):null;
		$fixed = ($this->_request->has('fixed'))?$this->_request->getParam('fiexed'):false;
	
	
	
		$speechCollection = new Application_Model_DbCollections_Speeches();
		$userCollection = new Application_Model_DbCollections_Users();
	
		try
		{
			
			$speechInfo = $speechCollection->findOne(array('speakerID' => $speakerID, 'subject' => $subject, 'description' => $description));
			if(!is_null($speechInfo))
				$this->returnJson(0, "The speech has already exist");
	
			$id = new MongoId($speakerID);
			$temp = array("_id" => $id);
			$userInfo = $userCollection->findOne($temp);
			if(is_null($userInfo))
				$this->returnJson(0, "The speaker did not exist");
	
	
			$newSpeech = array('subject' => $subject , 'description' => $description,
					'speakerID' => $speakerID, 'when' => $when,//for test
					'where' => $where,'fixed' => $fixed,'createdOn' => new MongoDate(time()));
	
			$result = $speechCollection->insert($newSpeech);
	
			if(!$result)
			{
				$this->returnJson(0, 'Insert action failed');
			}
	
			$this->returnJson(1, 'Insert Successfully',$result);
	
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Please check the id format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during create speech',$e->getMessage());
		}
	
	
	
	}
	
	/**
	 * Author: Shaon
	 * update a speech via subject, description, speakerID,when,where,fixed,createdOn
	 * Path: speeches/update-speech
	 * Result: return status 1 if update succefully.
	 *
	 */
	public function updateSpeechAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
	
		if (!$this->_request->has('speakerID') || !$this->_request->has('subject')
		|| !$this->_request->has('description') )
		{
			$this->returnJson(0, 'Parameters error');
		}
	
	
		$speakerID = $this->_request->getParam('speakerID');
		$subject = $this->_request->getParam('subject');;
		$description = $this->_request->getParam('description');
		$when = ($this->_request->has('when'))?$this->_request->getParam('when'):null;
		$where = ($this->_request->has('where'))?$this->_request->getParam('where'):null;
		$fixed = ($this->_request->has('fixed'))?$this->_request->getParam('fiexed'):false;
	
	
		$speechCollection = new Application_Model_DbCollections_Speeches();
		$userCollection = new Application_Model_DbCollections_Users();
		
		try
		{
			$id = new MongoId($this->_request->getParam('id'));
			$speechTemp = array("_id" => $id);
			
			$speechInfo = $speechCollection->findOne($speechTemp);
			if(is_null($speechInfo))
				$this->returnJson(0, "The speech did not exist");
	
			$id = new MongoId($speakerID);
			$userTemp = array("_id" => $id);
			$userInfo = $userCollection->findOne($userTemp);
			if(is_null($userInfo))
				$this->returnJson(0, "The speaker did not exist");
	
	
			$speechData = array('subject' => $subject , 'description' => $description,
					'speakerID' => $speakerID, 'when' => $when,//for test
					'where' => $where,'fixed' => $fixed,'createdOn' => new MongoDate(time()));
	
			$result = $speechCollection->update($speechTemp,$speechData);
	
			if(!$result)
			{
				$this->returnJson(0, 'Update action failed');
			}
	
			$this->returnJson(1, 'Update successfully',$result);
	

		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Check the parameters format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during update speech',$e->getMessage());
		}
	
	
	}
	
	/**
	 * Author: Shaon
	 * Delete a speech
	 * Path : users/del-speech
	 * Result: return status 1 if delete succefully.
	 *
	 */
	public function delSpeechAction()
	{
		if(!$this->_request->getMethod() == "GET")
		{
			$this->returnJson(0, "Please use the right request method");
		}
	
		if (!$this->_request->has('id'))
		{
			$this->returnJson(0, 'Parameters error');
		}
	
		$speechesCollection = new Application_Model_DbCollections_Speeches();
		$commentsCollection = new Application_Model_DbCollections_Comments();
		$interestsCollection = new Application_Model_DbCollections_Interests();
		$feedbackCollection = new Application_Model_DbCollections_Feedbacks();
		
		try
		{
			$id = new MongoId($this->_request->getParam('id'));
			$temp = array("_id" => $id);
			$speechesInfo = $speechesCollection->findOne($temp);
	
			if(is_null($speechesInfo))
				$this->returnJson(0, "The comment did not exist");
			
			//transaction process?
			$speechResult = $speechesCollection->remove($temp);
			$commentResult = $commentsCollection->remove(array('speechID' => $speechesInfo['_id']->__toString()));
			$feedbackResult = $feedbackCollection->remove(array('speechID' => $speechesInfo['_id']->__toString()));
			$interestResult = $interestsCollection->remove(array('speechID' => $speechesInfo['_id']->__toString()));
			
			
	
			if (!$speechResult || !$commentResult || !$feedbackResult || !$interestResult)
			{
				$this->returnJson(0, "Delete action failed");
			}
	
			$this->returnJson(1, "Delete successfully",true);
	
		}
		catch (MongoException $e)
		{
			$this->returnJson(0, 'Please check the id format',$e->getMessage());
		}
		catch (Zend_Exception $e)
		{
			$this->returnJson(0, 'Some errors occoured during delete user comment',$e->getMessage());
		}
	
	
	}
	
	
	
}