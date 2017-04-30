<?php
namespace ILC\BackOfficeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class SessionformationAddForm extends AbstractType
{

	/**
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('moduleformation', EntityType::class, array(
			'label' => 'sf.moduleformation',
			'class' => 'ILCDataBundle:Moduleformation',
			'choice_label' => 'code',
			'multiple' => false,
			'required' => true
		));
		$builder->add('code', TextType::class, array(
			'label' => 'sf.code',
			'required' => true
		));
		$builder->add('intitule', TextType::class, array(
			'label' => 'sf.intitule',
			'required' => true
		));
		$builder->add('datedebut', DateType::class, array(
			'label' => 'sf.datedebut',
			'required' => true
		));
		$builder->add('heuredebut', TimeType::class, array(
			'label' => 'sf.heuredebut',
			'required' => true
		));
		$builder->add('datefin', DateType::class, array(
			'label' => 'sf.datefin',
			'required' => true
		));
		$builder->add('heurefin', TimeType::class, array(
			'label' => 'sf.heurefin',
			'required' => true
		));
		$builder->add('lieu', TextType::class, array(
			'label' => 'sf.lieu',
			'required' => true
		));
		$builder->add('numcontactcentre', TextType::class, array(
			'label' => 'sf.numcontactcentre',
			'required' => true
		));
		$builder->add('conditionsreport', TextareaType::class, array(
			'label' => 'sf.conditionsreport',
			'required' => true
		));
		$builder->add('dateinfo', TextareaType::class, array(
			'label' => 'sf.dateinfo',
			'required' => false
		));
		$builder->add('otherinfo', TextareaType::class, array(
			'label' => 'sf.otherinfo',
			'required' => true
		));
		$builder->add('maxparticipants', IntegerType::class, array(
			'label' => 'sf.maxparticipants',
			'required' => true
		));
		$builder->add('verouillage', ChoiceType::class, array(
			'label' => 'sf.verouillage',
			'choices' => array(
				'Oui' => true,
				'Non' => false
			),
			'expanded' => true,
			'multiple' => false,
			'required' => true
		));
	}

	/**
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'sfaddform';
	}

	/**
	 *
	 * {@inheritdoc} @see AbstractType::getBlockPrefix()
	 */
	public function getBlockPrefix()
	{
		return $this->getName();
	}

	/**
	 *
	 * @param array $options
	 * @return multitype:
	 */
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'ILC\DataBundle\Entity\Sessionformation'
		);
	}
}
