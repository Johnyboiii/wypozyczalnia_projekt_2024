<?php

/**
 * ReservationType.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

/**
 * Reservation form type.
 */
class ReservationType extends AbstractType
{
    private Security $security;

    /**
     * Constructor.
     *
     * @param Security $security The security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * Build the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => !$this->security->getUser(),
            ])
            ->add('nickname', TextType::class)
            ->add('reservationComment', TextareaType::class);
    }

    /**
     * Configure the form options.
     *
     * @param OptionsResolver $resolver The resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
