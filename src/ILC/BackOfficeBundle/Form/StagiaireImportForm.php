<?php
namespace ILC\BackOfficeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 *
 * @author sasedev <seif.salah@gmail.com>
 */
class StagiaireImportForm extends AbstractType
{

	/**
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('excel', FileType::class, array(
			'label' => 'st.excel',
			'required' => true,
			'constraints' => array(
				new File(array(
					'mimeTypes' => array(
						'application/vnd.ms-office',
						'application/vnd.ms-excel',
						'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
						'application/zip'
					),
					// 'mimeTypesMessage' => 'st.excel.mimeTypes',
					'maxSize' => "4096k",
					'maxSizeMessage' => 'st.excel.maxsize'
				))
			),
			'mapped' => false
		));
	}

	/**
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'stagiaireimportform';
	}
}
