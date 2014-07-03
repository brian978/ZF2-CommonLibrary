<?php
/**
 * ZF2-ExtendedFramework
 *
 * @link      https://github.com/brian978/ZF2-ExtendedFramework
 * @copyright Copyright (c) 2013
 * @license   Creative Commons Attribution-ShareAlike 3.0
 */

namespace Acamar\Components;

use Zend\I18n\Translator\Translator;

trait TranslatorAwareObject
{
    /**
     * @var \Zend\I18n\Translator\Translator
     */
    protected $translator;

    /**
     * Sets translator to use in helper
     *
     * @param  Translator $translator  [optional] translator.
     *                                 Default is null, which sets no translator.
     * @param  string     $textDomain  [optional] text domain
     *                                 Default is null, which skips setTranslatorTextDomain
     * @return TranslatorAwareInterface
     */
    public function setTranslator(Translator $translator = null, $textDomain = null)
    {
        unset($textDomain);

        $this->translator = $translator;

        return $translator;
    }

    /**
     * Returns translator used in object
     *
     * @return Translator|null
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Checks if the object has a translator
     *
     * @return bool
     */
    public function hasTranslator()
    {
        return is_object($this->translator);
    }

    /**
     * Sets whether translator is enabled and should be used
     *
     * @param  bool $enabled [optional] whether translator should be used.
     *                       Default is true.
     * @return TranslatorAwareInterface
     */
    public function setTranslatorEnabled($enabled = true)
    {
        unset($enabled);

        return $this->translator;
    }

    /**
     * Returns whether translator is enabled and should be used
     *
     * @return bool
     */
    public function isTranslatorEnabled()
    {
        return true;
    }

    /**
     * Set translation text domain
     *
     * @param  string $textDomain
     * @return TranslatorAwareInterface
     */
    public function setTranslatorTextDomain($textDomain = 'default')
    {
        unset($textDomain);

        return $this->translator;
    }

    /**
     * Return the translation text domain
     *
     * @return string
     */
    public function getTranslatorTextDomain()
    {
        return 'default';
    }
}
