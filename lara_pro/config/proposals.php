<?php

return [
    'pdf' => [
        'browser_path' => env('PROPOSAL_PDF_BROWSER_PATH'),
        'timeout' => (int) env('PROPOSAL_PDF_TIMEOUT', 90),
    ],

    'templates' => [
        'corporate-green' => [
            'name' => 'Corporate Green Proposal',
            'description' => 'Deep forest editorial pages for consulting, finance, enterprise, and real estate proposals.',
            'category' => 'Corporate',
            'theme_key' => 'green',
            'sort_order' => 1,
            'palette' => [
                'primary' => '#143d32',
                'secondary' => '#0b241e',
                'accent' => '#8ccf5f',
                'surface' => '#f4f7f1',
                'ink' => '#f8fff5',
            ],
            'settings' => [
                'font_family' => 'Aptos',
                'header_style' => 'Minimal bar',
                'footer_style' => 'Reference footer',
            ],
        ],
        'modern-gold' => [
            'name' => 'Modern Gold Enterprise Proposal',
            'description' => 'White editorial spreads with yellow/gold accents for agencies, startups, and pitch work.',
            'category' => 'Enterprise',
            'theme_key' => 'gold',
            'sort_order' => 2,
            'palette' => [
                'primary' => '#111111',
                'secondary' => '#f3f4f0',
                'accent' => '#e8b51f',
                'surface' => '#ffffff',
                'ink' => '#171717',
            ],
            'settings' => [
                'font_family' => 'Aptos Condensed',
                'header_style' => 'Editorial split',
                'footer_style' => 'Gold folio',
            ],
        ],
        'minimal-white' => [
            'name' => 'Minimal White Proposal',
            'description' => 'Quiet white-space-led business document with precise dividers and understated brand color.',
            'category' => 'Minimal',
            'theme_key' => 'white',
            'sort_order' => 3,
            'palette' => [
                'primary' => '#1f2937',
                'secondary' => '#f6f7f9',
                'accent' => '#4f8f7a',
                'surface' => '#ffffff',
                'ink' => '#20242a',
            ],
            'settings' => [
                'font_family' => 'Segoe UI',
                'header_style' => 'Clean masthead',
                'footer_style' => 'Thin line',
            ],
        ],
        'dark-premium' => [
            'name' => 'Dark Premium Proposal',
            'description' => 'High-contrast charcoal proposal with cinematic image panels and executive section blocks.',
            'category' => 'Premium',
            'theme_key' => 'dark',
            'sort_order' => 4,
            'palette' => [
                'primary' => '#15171c',
                'secondary' => '#252a32',
                'accent' => '#d5aa59',
                'surface' => '#f6f3ee',
                'ink' => '#f9fafb',
            ],
            'settings' => [
                'font_family' => 'Aptos',
                'header_style' => 'Dark executive',
                'footer_style' => 'Luxury footer',
            ],
        ],
        'creative-agency' => [
            'name' => 'Creative Agency Proposal',
            'description' => 'Grid-based creative proposal with bold headings, flexible services, and portfolio-friendly pages.',
            'category' => 'Creative',
            'theme_key' => 'agency',
            'sort_order' => 5,
            'palette' => [
                'primary' => '#161616',
                'secondary' => '#f2f5f7',
                'accent' => '#4ab3c7',
                'surface' => '#ffffff',
                'ink' => '#151515',
            ],
            'settings' => [
                'font_family' => 'Inter',
                'header_style' => 'Agency grid',
                'footer_style' => 'Project folio',
            ],
        ],
    ],

    'sections' => [
        ['type' => 'cover', 'title' => 'Cover Page', 'eyebrow' => 'Business Proposal', 'body' => 'A polished opening page that introduces the engagement, client, brand, date, and proposal reference.'],
        ['type' => 'welcome', 'title' => 'Welcome / Introduction', 'eyebrow' => 'Welcome', 'body' => 'Thank you for the opportunity to present this proposal. We have shaped this document to clarify the opportunity, outline the recommended solution, and make the next decision simple.'],
        ['type' => 'executive_summary', 'title' => 'Executive Summary', 'eyebrow' => 'Executive Summary', 'body' => 'This proposal outlines a focused engagement designed to improve business clarity, strengthen customer trust, and deliver a high-quality outcome through a structured process.'],
        ['type' => 'table_of_contents', 'title' => 'Table of Contents', 'eyebrow' => 'Contents', 'body' => 'A navigable overview of the proposal sections and page structure.'],
        ['type' => 'about_company', 'title' => 'About Our Company', 'eyebrow' => 'Company Profile', 'body' => 'We help organizations turn strategy into refined digital and operational experiences through careful planning, strong design judgment, and disciplined delivery.'],
        ['type' => 'vision_mission', 'title' => 'Vision and Mission', 'eyebrow' => 'Our Direction', 'body' => 'Our mission is to create clear, credible, and high-performing business experiences. Our vision is to become a trusted partner for organizations that value excellence and measurable progress.'],
        ['type' => 'problem_statement', 'title' => 'Problem Statement', 'eyebrow' => 'The Challenge', 'body' => 'The current opportunity requires a solution that improves clarity, reduces friction, and creates a more persuasive experience for stakeholders and customers.'],
        ['type' => 'proposed_solution', 'title' => 'Proposed Solution', 'eyebrow' => 'Recommended Solution', 'body' => 'We recommend a phased approach that aligns strategy, execution, review, and handoff so the final result is polished, practical, and ready for real business use.'],
        ['type' => 'scope_of_work', 'title' => 'Scope of Work', 'eyebrow' => 'Engagement Scope', 'body' => 'The work includes discovery, planning, design direction, implementation, review, launch support, and the agreed deliverables listed in this proposal.'],
        ['type' => 'services_offered', 'title' => 'Services Offered', 'eyebrow' => 'Services', 'body' => 'Our services are organized into strategic planning, experience design, delivery support, content refinement, technical implementation, and stakeholder-ready documentation.'],
        ['type' => 'project_objectives', 'title' => 'Project Objectives', 'eyebrow' => 'Objectives', 'body' => 'The objective is to deliver a premium solution that increases confidence, communicates value clearly, and supports measurable business momentum.'],
        ['type' => 'work_process', 'title' => 'Work Process', 'eyebrow' => 'Process', 'body' => 'We move through discovery, direction, production, review, and launch with clear checkpoints and transparent communication at each stage.'],
        ['type' => 'timeline', 'title' => 'Timeline / Milestones', 'eyebrow' => 'Timeline', 'body' => 'The proposed timeline is arranged into clear phases with defined deliverables, start dates, end dates, duration, and status.'],
        ['type' => 'deliverables', 'title' => 'Deliverables', 'eyebrow' => 'Deliverables', 'body' => 'Final deliverables include the approved work products, supporting assets, documentation, launch support, and any agreed handoff materials.'],
        ['type' => 'pricing', 'title' => 'Pricing / Investment Table', 'eyebrow' => 'Investment', 'body' => 'The pricing table summarizes services, quantities, unit pricing, discounts, tax or VAT, and the final investment total.'],
        ['type' => 'terms', 'title' => 'Terms and Conditions', 'eyebrow' => 'Terms', 'body' => 'Work begins after acceptance and initial payment. Timelines depend on timely feedback, access to required materials, and approval at key milestones.'],
        ['type' => 'agreement', 'title' => 'Project Agreement', 'eyebrow' => 'Agreement', 'body' => 'By accepting this proposal, both parties agree to the scope, timeline, payment structure, review process, and project responsibilities outlined here.'],
        ['type' => 'team', 'title' => 'Team Members', 'eyebrow' => 'Team', 'body' => 'A focused project team will guide strategy, execution, communication, and final delivery.'],
        ['type' => 'case_studies', 'title' => 'Case Studies / Portfolio', 'eyebrow' => 'Proof of Work', 'body' => 'Selected portfolio examples demonstrate how similar work has improved clarity, credibility, experience quality, and commercial outcomes.'],
        ['type' => 'testimonials', 'title' => 'Testimonials', 'eyebrow' => 'Client Confidence', 'body' => 'Client feedback reflects our commitment to clear communication, premium execution, and dependable delivery.'],
        ['type' => 'acceptance', 'title' => 'Acceptance Page', 'eyebrow' => 'Acceptance', 'body' => 'Please sign and return this page to confirm acceptance of the proposal, investment, scope, and project terms.'],
        ['type' => 'closing', 'title' => 'Closing Page', 'eyebrow' => 'Next Step', 'body' => 'We look forward to partnering with you and moving this project into a confident, well-managed execution phase.'],
    ],

    'pricing_items' => [
        ['package' => 'Basic', 'service_name' => 'Discovery and strategic direction', 'description' => 'Project alignment, requirements review, and delivery plan.', 'quantity' => 1, 'unit_price' => 1500, 'discount' => 0, 'tax_rate' => 0],
        ['package' => 'Standard', 'service_name' => 'Design and proposal implementation', 'description' => 'Core execution, review cycle, and polished handoff.', 'quantity' => 1, 'unit_price' => 3500, 'discount' => 0, 'tax_rate' => 0],
        ['package' => 'Premium', 'service_name' => 'Premium delivery and launch support', 'description' => 'Advanced execution, stakeholder polish, and launch support.', 'quantity' => 1, 'unit_price' => 6500, 'discount' => 0, 'tax_rate' => 0],
    ],

    'timeline' => [
        ['phase_title' => 'Discovery', 'description' => 'Requirements, goals, audience, and project success criteria.', 'duration' => '1 week', 'deliverables' => 'Brief, roadmap, content checklist', 'status' => 'Planned'],
        ['phase_title' => 'Direction', 'description' => 'Creative direction, structure, and stakeholder alignment.', 'duration' => '1 to 2 weeks', 'deliverables' => 'Approved direction and page plan', 'status' => 'Planned'],
        ['phase_title' => 'Production', 'description' => 'Implementation, refinement, QA, and final approval.', 'duration' => '2 to 4 weeks', 'deliverables' => 'Final deliverables and handoff pack', 'status' => 'Planned'],
    ],

    'team' => [
        ['name' => 'Project Lead', 'role' => 'Strategy and Delivery', 'bio' => 'Owns the project rhythm, stakeholder alignment, and final delivery quality.', 'email' => 'hello@example.com', 'social_link' => ''],
        ['name' => 'Design Lead', 'role' => 'Creative Direction', 'bio' => 'Shapes the visual system, section hierarchy, and presentation polish.', 'email' => 'design@example.com', 'social_link' => ''],
        ['name' => 'Implementation Lead', 'role' => 'Technical Execution', 'bio' => 'Turns approved direction into production-ready work with careful QA.', 'email' => 'delivery@example.com', 'social_link' => ''],
    ],
];
