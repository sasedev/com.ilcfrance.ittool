<?php
namespace ILC\BackOfficeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class StagiaireAddForm extends AbstractType
{

	/**
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('username', TextType::class, array(
			'label' => 'st.username',
			'required' => true
		));
		$builder->add('nom', TextType::class, array(
			'label' => 'st.nom',
			'required' => true
		));
		$builder->add('prenom', TextType::class, array(
			'label' => 'st.prenom',
			'required' => true
		));
		$builder->add('email', EmailType::class, array(
			'label' => 'st.email',
			'required' => true
		));
		$builder->add('tel', TextType::class, array(
			'label' => 'st.tel',
			'required' => true
		));
		$builder->add('nomcontact', TextType::class, array(
			'label' => 'st.nomcontact',
			'required' => true
		));
		$builder->add('emailcontact', EmailType::class, array(
			'label' => 'st.emailcontact',
			'required' => true
		));
	}

	/**
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'stagiaireaddform';
	}

	/**
	 *
	 * @param array $options
	 * @return multitype:
	 */
	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => 'ILC\DataBundle\Entity\Stagiaire'
		);
	}
}
