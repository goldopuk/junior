<?php

namespace AppBundle\Entity;

/**
 * Question
 */
class Question
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $description;

    /**
     * @var boolean
     */
    private $answer;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var string
     */
    private $subtheme;

    /**
     * @var string
     */
    private $slug;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Question
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set answer
     *
     * @param boolean $answer
     *
     * @return Question
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return boolean
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set theme
     *
     * @param string $theme
     *
     * @return Question
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Set subtheme
     *
     * @param string $subtheme
     *
     * @return Question
     */
    public function setSubtheme($subtheme)
    {
        $this->subtheme = $subtheme;

        return $this;
    }

    /**
     * Get subtheme
     *
     * @return string
     */
    public function getSubtheme()
    {
        return $this->subtheme;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Question
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}

