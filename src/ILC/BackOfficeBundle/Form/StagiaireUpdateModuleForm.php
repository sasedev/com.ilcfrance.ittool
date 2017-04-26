<?php
namespace ILC\BackOfficeBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class StagiaireUpdateModuleForm extends AbstractType
{

	/**
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('modules', EntityType::class, array(
			'class' => 'ILCDataBundle:Moduleformation',
			'query_builder' => function (EntityRepository $er) {
				return $er->createQueryBuilder('m')->orderBy('m.intitule', 'ASC');
			},
			'expanded' => true,
			'multiple' => true
		));
	}

	/**
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'stagiaireupdatemodsform';
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
