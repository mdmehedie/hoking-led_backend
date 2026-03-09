<?php

namespace Database\Seeders;

use App\Models\CertificationAward;
use Illuminate\Database\Seeder;

class CertificationAwardSeeder extends Seeder
{
    public function run(): void
    {
        $certifications = [
            [
                'title' => 'ISO 9001:2015 Quality Management System',
                'slug' => 'iso-9001-2015-quality-management',
                'issuing_organization' => 'International Organization for Standardization',
                'date_awarded' => '2023-06-15',
                'description' => 'Certified for implementing and maintaining a quality management system that demonstrates our commitment to delivering consistent, high-quality products and services to our customers.',
                'image_path' => 'cert-iso-9001.jpg',
                'is_visible' => true,
                'sort_order' => 1,
                'meta_title' => 'ISO 9001:2015 Certification | Quality Management System',
                'meta_description' => 'Our ISO 9001:2015 certification demonstrates our commitment to quality management and customer satisfaction.',
                'meta_keywords' => 'ISO 9001, quality management, certification, standards'
            ],
            [
                'title' => 'ISO/IEC 27001:2013 Information Security Management',
                'slug' => 'iso-iec-27001-2013-information-security',
                'issuing_organization' => 'International Organization for Standardization',
                'date_awarded' => '2023-08-22',
                'description' => 'Certified for establishing, implementing, maintaining, and continually improving an information security management system within our organization.',
                'image_path' => 'cert-iso-27001.jpg',
                'is_visible' => true,
                'sort_order' => 2,
                'meta_title' => 'ISO/IEC 27001:2013 Certification | Information Security',
                'meta_description' => 'Our information security management certification ensures protection of client data and information assets.',
                'meta_keywords' => 'ISO 27001, information security, cybersecurity, data protection'
            ],
            [
                'title' => 'AWS Certified Solutions Architect - Professional Level',
                'slug' => 'aws-certified-solutions-architect-professional',
                'issuing_organization' => 'Amazon Web Services',
                'date_awarded' => '2023-04-10',
                'description' => 'Professional-level certification demonstrating expertise in designing distributed systems on AWS, including our ability to provide best practices recommendations and guidance on architectural design.',
                'image_path' => 'cert-aws-sa.jpg',
                'is_visible' => true,
                'sort_order' => 3,
                'meta_title' => 'AWS Certified Solutions Architect Professional | Cloud Expertise',
                'meta_description' => 'AWS Solutions Architect certification validates our expertise in designing scalable cloud infrastructure solutions.',
                'meta_keywords' => 'AWS, cloud computing, solutions architect, certification, Amazon Web Services'
            ],
            [
                'title' => 'Microsoft Certified: Azure Solutions Architect Expert',
                'slug' => 'microsoft-certified-azure-solutions-architect-expert',
                'issuing_organization' => 'Microsoft Corporation',
                'date_awarded' => '2023-09-05',
                'description' => 'Expert-level certification validating advanced skills in designing solutions that run on Microsoft Azure, including compute, network, storage, and security.',
                'image_path' => 'cert-azure-expert.jpg',
                'is_visible' => true,
                'sort_order' => 4,
                'meta_title' => 'Microsoft Certified Azure Solutions Architect Expert | Cloud Expertise',
                'meta_description' => 'Azure Solutions Architect Expert certification demonstrates our advanced Microsoft cloud platform expertise.',
                'meta_keywords' => 'Microsoft Azure, cloud computing, solutions architect, certification, Microsoft'
            ],
            [
                'title' => 'Certified Kubernetes Administrator (CKA)',
                'slug' => 'certified-kubernetes-administrator-cka',
                'issuing_organization' => 'Cloud Native Computing Foundation',
                'date_awarded' => '2023-11-18',
                'description' => 'Certification validating the skills, knowledge, and competency to perform the responsibilities of a Kubernetes administrator, including application lifecycle management, installation, configuration, and troubleshooting.',
                'image_path' => 'cert-cka.jpg',
                'is_visible' => true,
                'sort_order' => 5,
                'meta_title' => 'Certified Kubernetes Administrator (CKA) | Container Orchestration',
                'meta_description' => 'CKA certification validates our Kubernetes administration skills for container orchestration and management.',
                'meta_keywords' => 'Kubernetes, CKA, containers, orchestration, cloud native, DevOps'
            ],
            [
                'title' => 'Google Cloud Professional - Cloud Architect',
                'slug' => 'google-cloud-professional-cloud-architect',
                'issuing_organization' => 'Google LLC',
                'date_awarded' => '2023-07-12',
                'description' => 'Professional-level certification demonstrating expertise in designing and planning a cloud solution architecture, managing implementations, and ensuring solution and operations reliability.',
                'image_path' => 'cert-gcp-architect.jpg',
                'is_visible' => true,
                'sort_order' => 6,
                'meta_title' => 'Google Cloud Professional Cloud Architect | Google Cloud Expertise',
                'meta_description' => 'Google Cloud Professional certification validates our cloud architecture and implementation expertise.',
                'meta_keywords' => 'Google Cloud, GCP, cloud computing, cloud architect, certification, Google'
            ],
            [
                'title' => 'Certified Information Systems Security Professional (CISSP)',
                'slug' => 'certified-information-systems-security-professional-cissp',
                'issuing_organization' => 'ISC²',
                'date_awarded' => '2023-10-30',
                'description' => 'Premier cybersecurity certification demonstrating expertise in designing, implementing, and managing cybersecurity programs to protect organizations from cyber threats.',
                'image_path' => 'cert-cissp.jpg',
                'is_visible' => true,
                'sort_order' => 7,
                'meta_title' => 'CISSP Certification | Information Systems Security Professional',
                'meta_description' => 'CISSP certification validates our advanced cybersecurity expertise and commitment to information security.',
                'meta_keywords' => 'CISSP, cybersecurity, information security, ISC², security professional'
            ],
            [
                'title' => 'Project Management Professional (PMP)',
                'slug' => 'project-management-professional-pmp',
                'issuing_organization' => 'Project Management Institute',
                'date_awarded' => '2023-05-25',
                'description' => 'Globally recognized certification demonstrating competence in leading and directing projects and teams, ensuring project success within organizational constraints.',
                'image_path' => 'cert-pmp.jpg',
                'is_visible' => true,
                'sort_order' => 8,
                'meta_title' => 'PMP Certification | Project Management Professional',
                'meta_description' => 'PMP certification validates our project management expertise and commitment to project success.',
                'meta_keywords' => 'PMP, project management, PMI, project leadership, certification'
            ]
        ];

        foreach ($certifications as $certificationData) {
            CertificationAward::updateOrCreate(
                ['slug' => $certificationData['slug']],
                $certificationData
            );
        }

        $this->command->info('Certifications and awards seeded successfully!');
    }
}
