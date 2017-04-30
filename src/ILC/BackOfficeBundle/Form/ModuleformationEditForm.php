<?php
namespace ILC\BackOfficeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class ModuleformationEditForm extends AbstractType
{

	/**
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('code', TextType::class, array(
			'label' => 'mf.code',
			'required' => true
		));
		$builder->add('intitule', TextType::class, array(
			'label' => 'mf.intitule',
			'required' => true
		));
		$builder->add('description', TextareaType::class, array(
			'label' => 'mf.description',
			'required' => true
		));
		$builder->add('groupmodule', EntityType::class, array(
			'class' => 'ILCDataBundle:Groupmodule',
			'label' => 'mf.groupmodule',
			'property' => 'name',
			'multiple' => false,
			'required' => true
		));
		$builder->add('groupmodule', EntityType::class, array(
			'label' => 'mf.groupmodule',
			'class' => 'ILCDataBundle:Groupmodule',
			'choice_label' => 'name',
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
		return 'modeditform';
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
			'data_class' => 'ILC\DataBundle\Entity\Moduleformation'
		);
	}
}
