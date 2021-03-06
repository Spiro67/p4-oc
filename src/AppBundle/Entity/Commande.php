<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * achat.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommandeRepository")
 * @ORM\Table("_commande")
 */
class Commande {
	
	 /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_commande", type="datetime", nullable=false)
     *
     */
    private $dateCommande;
	
	/**
     * @var \DateTime
     *
     * @ORM\Column(name="date_entree", type="datetime", nullable=false)
     *
     */
    private $dateEntree;
	
	/**
     * @var string
     *
     * @ORM\Column(name="typeBillet", type="string", length=100, nullable=false)
     *
     */
    private $typeBillet;
	
		/**
     * @var int
     *
     * @ORM\Column(name="quantite", nullable=false)
     *
     */
	private $quantite;
	
	/**
     * @var string
     *
     * @ORM\Column(name="prixCommande", type="string")
     *
     */
    private $prixCommande;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=150, nullable=false)
     *
     */
    private $email;

    /**
     *
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\info", mappedBy="commande", cascade={"PERSIST", "REMOVE"})
     *
     */
    private $infos;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_commande", type="string", nullable=true)
     *
     */
    private $numeroCommande;


    public function __construct(){

        $this->dateCommande = new \DateTime();
        $this->infos  = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return string
     */

    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateEntree
     *
     * @param \DateTime $dateEntree
     *
     * @return Commande
     */
    public function setDateEntree($dateEntree)
    {
        $this->dateEntree = $dateEntree;

        return $this;
    }

    /**
     * Get dateEntree
     *
     * @return \DateTime
     */
    public function getDateEntree()
    {
        return $this->dateEntree;
    }

    /**
     * Set typeBillet
     *
     * @param string $typeBillet
     *
     * @return Commande
     */
    public function setTypeBillet($typeBillet)
    {
        $this->typeBillet = $typeBillet;

        return $this;
    }

    /**
     * Get typeBillet
     *
     * @return string
     */
    public function getTypeBillet()
    {
        return $this->typeBillet;
    }

    /**
     * Set quantite
     *
     * @param string $quantite
     *
     * @return Commande
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return string
     */

    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Commande
     */

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */

    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dateCommande
     *
     * @param \DateTime $dateCommande
     *
     * @return Commande
     */
    public function setDateCommande($dateCommande)
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    /**
     * Get dateCommande
     *
     * @return \DateTime
     */
    public function getDateCommande()
    {
        return $this->dateCommande;
    }

    /**
     * Set prixCommande
     *
     * @param string $prixCommande
     *
     * @return Commande
     */
    public function setPrixCommande($prixCommande)
    {
        $this->prixCommande = $prixCommande;

        return $this;
    }

    /**
     * Get prixCommande
     *
     * @return string
     */
    public function getPrixCommande()
    {
        return $this->prixCommande;
    }

    /**
     * Set numeroCommande
     *
     * @param string $prixCommande
     *
     * @return Commande
     */
    public function setNumeroCommande($numeroCommande)
    {
        $this->numeroCommande = $numeroCommande;

        return $this;
    }

    /**
     * Get numeroCommande
     *
     * @return string
     */
    public function getNumeroCommande()
    {
        return $this->numeroCommande;
    }

    /**
     * Add info.
     *
     * @param \AppBundle\Entity\Info $info
     *
     * @return Commande
     */
    public function addInfo(Info $info)
    {
        $this->infos[] = $info;

        return $this;
    }

    /**
     * Remove info.
     *
     * @param \AppBundle\Entity\Info $info
     */
    public function removeInfo(Info $info)
    {
        $this->infos->removeElement($info);
    }

    /**
     * Get infos.
     *
     * @return \AppBundle\Entity\Commande
     */
    public function getInfos()
    {
        return $this->infos;
    }
}
