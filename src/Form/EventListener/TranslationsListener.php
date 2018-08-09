<?php

namespace GedmoTranslationFormBundle\Form\EventListener;

use GedmoTranslationFormBundle\Form\Type\TranslationType;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;
use Gedmo\Translatable\Translatable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TranslationsListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $locales;

    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * @param array $locales
     * @param string $defaultLocale
     */
    public function __construct(array $locales, $defaultLocale)
    {
        $this->locales = $locales;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SUBMIT => 'postSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $options = $form->getConfig()->getOptions();
        $entity = $form->getRoot()->getData();
        $propertyName = $form->getPropertyPath()->__toString();
        foreach ($this->locales as $locale) {
            if (!method_exists($entity, 'getTranslations')) {
                throw new InvalidConfigurationException(
                    'Please specify getTranslations method. Follow gide by Personal translations in Gedmo.'
                );
            }
            $translations = $entity->getTranslations();
            $emptyData = '';
            foreach ($translations as $translation) {
                if ($translation->getLocale() === $locale && $translation->getField() === $propertyName) {
                    $emptyData = $translation->getContent();
                    break;
                }
            }

            if (isset($options[TranslationType::FIELD_OPTIONS]['attr'])) {
                $attribute = $options[TranslationType::FIELD_OPTIONS]['attr'];
            } else {
                $attribute = [];
            }

            $attribute['is_active_tab'] = $locale === $this->defaultLocale ? true : false;
            $attribute['unique'] = uniqid($locale);
            $attribute['is_bool'] = false;

            if (in_array($options[TranslationType::TYPE], [CheckboxType::class])) {
                $emptyData = boolval($emptyData);
                $attribute['is_bool'] = true;
            }

            $form->add(
                $locale.'_content',
                $options[TranslationType::TYPE],
                array_merge(
                    [
                        'label' => $locale,
                        'data' => $emptyData
                    ],
                    $options[TranslationType::FIELD_OPTIONS],
                    [
                        'attr' => $attribute
                    ]
                )
            );
        }
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $entity = $form->getRoot()->getData();
        $propertyName = $form->getPropertyPath()->__toString();
        foreach ($this->locales as $locale) {
            $data = $form->get($locale . '_content')->getData();
            $needTranslation = null;
            if (!method_exists($entity, 'getTranslations')) {
                throw new InvalidConfigurationException(
                    'Please specify getTranslations method. Follow gide by Personal translations in Gedmo.'
                );
            }
            foreach ($entity->getTranslations() as $translation) {
                if ($translation->getLocale() === $locale && $translation->getField() === $propertyName) {
                    $needTranslation = $translation;
                    $needTranslation->setContent($data);
                    $this->setDataToEntity($entity, $needTranslation, $locale);

                    break;
                }
            }

            if ($needTranslation === null) {
                $className = sprintf('%sTranslation', get_class($entity));
                $objectTranslation = new $className($locale, $propertyName, $data);

                if (!method_exists($entity, 'addTranslation')) {
                    throw new InvalidConfigurationException(
                        'Please specify addTranslation method. Follow gide by Personal translations in Gedmo.'
                    );
                }

                $entity->addTranslation($objectTranslation);
                $objectTranslation->setObject($entity);

                $this->setDataToEntity($entity, $objectTranslation, $locale);
            }
        }
    }

    /**
     * @param object $entity
     * @param object $objectTranslation
     * @param string $locale
     */
    protected function setDataToEntity($entity, $objectTranslation, $locale)
    {
        $method = sprintf('set%s', $objectTranslation->getField());

        if(method_exists($entity, $method) && $this->defaultLocale === $locale) {
            $entity->$method($objectTranslation->getContent());
        }
    }
}