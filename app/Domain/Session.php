<?php
namespace HendyNurSholeh\Domain;
class Session {
	
	public function __construct(
		private string $id,
		private string $userId
    ){}


	/**
	 * @return string
	 */
	public function getId(): string {
		return $this->id;
	}
	
	/**
	 * @param string $id 
	 * @return self
	 */
	public function setId(string $id): self {
		$this->id = $id;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUserId(): string {
		return $this->userId;
	}
	
	/**
	 * @param string $userId 
	 * @return self
	 */
	public function setUserId(string $userId): self {
		$this->userId = $userId;
		return $this;
	}
}