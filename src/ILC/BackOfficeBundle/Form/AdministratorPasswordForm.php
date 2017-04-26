<?php
namespace ILC\BackOfficeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class AdministratorPasswordForm extends AbstractType
{

	/**
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('password', RepeatedType::class, array(
			'type' => PasswordType::class,
			'invalid_message' => 'adm.cpasswd.notequal',
			'options' => array(
				'label' => 'adm.password'
			),
			'first_name' => 'passwd',
			'second_name' => 'cpasswd'
		));
	}

	/**
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'admpassform';
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
