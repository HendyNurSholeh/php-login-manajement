<?php
namespace HendyNurSholeh\Domain;
class Session {

    public function __construct(
        private string $id,
        private string $id_user
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
	public function getId_user(): string {
		return $this->id_user;
	}
	
	/**
	 * @param string $id_user 
	 * @return self
	 */
	public function setId_user(string $id_user): self {
		$this->id_user = $id_user;
		return $this;
	}
}