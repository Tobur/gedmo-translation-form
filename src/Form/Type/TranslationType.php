<?php

namespace GedmoTranslationFormBundle\Form\Type;

use GedmoTranslationFormBundle\Form\EventListener\TranslationsListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslationType extends AbstractType
{
    const TYPE = 'type';
    const FIELD_OPTIONS = 'field_options';
    const REUIRED_LOCALES = 'required_locales';

    /**
     * @var TranslationsListener
     */
    protected $translationListener;

    /**
     * @param TranslationsListener $translationsListener
     */
    public function __construct(TranslationsListener $translationsListener)
    {
        $this->translationListener = $translationsListener;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->translationListener);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            static::TYPE => null,
            static::FIELD_OPTIONS => [],
            static::REUIRED_LOCALES => [],
            'mapped' => false
        ]);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'translatable';
    }

    public function getBlockPrefix()
    {
        return 'translatable';
    }
}