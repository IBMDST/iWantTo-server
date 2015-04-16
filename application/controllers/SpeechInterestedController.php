<?php
class SpeechInterestedController extends My_Center_Controller
{
	public function indexAction()
	{
		try
		{
			$method = $this->_request->getMethod();
			switch ($method)
			{
				case "GET":
					
					if ($this -> _request->has('userID'))
					{
						$interestsCollection = new Application_Model_DbCollections_Interests();
						try
						{
							$id = $this->_request->getParam('userID');
							$temp = array("userID" => $id);
							if($this->_request->has('speechID'))
								$temp ['speechID']= $this->_request->getParam('speechID');

							$interestInfo = $interestsCollection->find($temp);
						
							if(is_null($interestInfo))
								$this->returnJson(400, "The user did not exist");
						
							$interests = array();
							if($interestInfo instanceof MongoCursor)
							{
								foreach ($interestInfo as $interest)
								{
									$tempInterest["userID"] =  $interest['userID'];
									$tempInterest["speechID"] =  $interest['speechID'];
									$tempInterest["createdOn"] =  $interest['createdOn']->sec;
									$tempInterest["id"] =  $interest['_id']->__toString();							
									$interests[] = $tempInterest;
								}
							}
						
							$this->returnJson(200, "Get interests successfully",$interests);
						
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
					else if ($this -> _request->has('id'))
					{
						$interestsCollection = new Application_Model_DbCollections_Interests();
						$usersCollection = new Application_Model_DbCollections_Users();
						try
						{
							$id = $this->_request->getParam('id');
							$temp = array("speechID" => $id);
							$interestInfo = $interestsCollection->find($temp);
	
							$interests = array();
							if($interestInfo instanceof MongoCursor)
							{
								foreach ($interestInfo as $interest)
								{
									$tempInterest["userID"] =  $interest['userID'];
									$tempInterest["speechID"] =  $interest['speechID'];
									$tempInterest["createdOn"] =  $interest['createdOn']->sec;
									$tempInterest["id"] =  $interest['_id']->__toString();
									$tempInterest['userName'] =  $usersCollection->findOne(array('_id' => new MongoId($interest['userID'])))['username'];;
							
									$interests[] = $tempInterest;
								}
							}
								
							if(is_null($interestInfo))
								$this->returnJson(200, "The feedback did not exist");
				
						
							$this->returnJson(200, "Get interests successfully",$interests);
								
						}
						catch (MongoException $e)
						{
							$this->returnJson(400, 'Please check the id format'.$e->getMessage());
						}
						catch (Zend_Exception $e)
						{
							$this->returnJson(500, 'Some errors occoured during get one speech interest'.$e->getMessage());
						}
						
					}
					else{
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
											'speechID' => $item['speechID'], 'createdOn' => $item['createdOn']->sec);
									$result[] = $temp;
								}
						
								$this->returnJson(200, "Get all users interests successfully", $result);
							}
							else
							{
								$this->returnJson(200, 'no available data');
							}
						
						
						}
						catch (Zend_Exception $e)
						{
							$this->returnJson(500, 'Some errors occoured during get user interests',$e->getMessage());
						}
						
					}
						
					break;
		
				case "POST":
					if (!$this->_request->has('userID') || !$this->_request->has('speechID'))
					{
						$this->returnJson(400, 'Parameters error');
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
							$this->returnJson(400, "The speech did not exist");
							
						$id = new MongoId($userID);
						$temp = array("_id" => $id);
						$userInfo = $userCollection->findOne($temp);
						if(is_null($userInfo))
							$this->returnJson(400, "The user did not exist");
							
							
						$temp = array("speechID" => $speechID, "userID" => $userID);
						$interestInfo = $interestsCollection->findOne($temp);
					
						if(isset($interestInfo)&&!empty($interestInfo))
							$this->returnJson(400, "The interest has already exist");
							
						$newInterest = array('userID' => $userID , 'speechID' => $speechID, 'createdOn' => new MongoDate(time()));
							
						$result = $interestsCollection->insert($newInterest);
							
						if(!$result)
						{
							$this->returnJson(500, 'Insert action failed');
						}
						
						$interestTemp = $interestsCollection -> findOne(array('speechID' => $speechID,'userID' => $userID));
						$interest['id'] = $interestTemp['_id']->__toString();
						$interest['userID'] = $interestTemp['userID'];
						$interest['speechID'] = $interestTemp['speechID'];
						$interest['createdOn'] = $interestTemp['createdOn']->sec;
						
							
						$this->returnJson(200, 'Insert Successfully',$interest);
					
					}
					catch (MongoException $e)
					{
						$this->returnJson(400, 'Please check the id format'.$e->getMessage());
					}
					catch (Zend_Exception $e)
					{
						$this->returnJson(500, 'Some errors occoured during create user profile'.$e->getMessage());
					}
					break;
				case "PUT":
					//echo $this->_request->getMethod();
					break;
				case "DELETE":
					if (!$this->_request->has('id'))
					{
						$this->returnJson(400, 'Parameters error');
					}
					
					$interestsCollection = new Application_Model_DbCollections_Interests();
					
					try
					{
						$id = new MongoId($this->_request->getParam('id'));
						$temp = array("_id" => $id);
						$interestInfo = $interestsCollection->findOne($temp);
					
						if(is_null($interestInfo))
							$this->returnJson(400, "The interest did not exist");
							
						$result = $interestsCollection->remove($temp);
							
						if (!$result)
						{
							$this->returnJson(500, "Delete action failed");
						}
							
						$this->returnJson(200, "Delete successfully",$result);
							
					}
					catch (MongoException $e)
					{
						$this->returnJson(400, 'Please check the id format'.$e->getMessage());
					}
					catch (Zend_Exception $e)
					{
						$this->returnJson(500, 'Some errors occoured during delete user profile'.$e->getMessage());
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
