<?php
namespace ILC\BackOfficeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class AdministratorProfileForm extends AbstractType
{

	/**
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('name', TextType::class, array(
			'label' => 'adm.name',
			'required' => true
		));
		$builder->add('email', EmailType::class, array(
			'label' => 'adm.email',
			'required' => true
		));
	}

	/**
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'admprofileform';
	}

	/**
	 *
	 * @param array $options
	 * @return multitype:
	 */
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'ILC\DataBundle\Entity\Administrator'
		);
	}
}
