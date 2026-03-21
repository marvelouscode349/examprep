<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class topicsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $syllabus = [

            // ================================================================
            // ENGLISH LANGUAGE (subject_id: 2)
            // ================================================================
            2 => [
                [
                    'name'      => 'Comprehension and Summary',
                    'order'     => 1,
                    'subtopics' => [
                        'Description, narration, exposition, argumentation and persuasion',
                        'Comprehension of passages: whole and parts',
                        'Comprehension of words, phrases, clauses, figures of speech and idioms',
                        'Coherence and logical reasoning: deductions and inferences',
                        'Synthesis of ideas from passages',
                        'Cloze test passages',
                        'Reading text: The Lekki Headmaster by Kabir Alabi Garba',
                    ],
                    'objectives' => [
                        'Identify main points and topic sentences in passages',
                        'Determine implied meaning',
                        'Identify grammatical functions of words, phrases and clauses',
                        'Identify figurative and idiomatic expressions in context',
                        'Deduce the writer\'s intentions including mood, attitude and opinion',
                    ],
                ],
                [
                    'name'      => 'Lexis and Structure',
                    'order'     => 2,
                    'subtopics' => [
                        'Synonyms and antonyms',
                        'Homonyms',
                        'Clause and sentence patterns',
                        'Word classes and their functions',
                        'Mood, tense, aspect, number, agreement and concord',
                        'Degree: positive, comparative and superlative',
                        'Question tags',
                        'Punctuation and spelling',
                        'Ordinary usage, figurative usage and idiomatic usage',
                    ],
                    'objectives' => [
                        'Identify words and expressions in ordinary, figurative and idiomatic contexts',
                        'Determine similar and opposite meanings of words',
                        'Differentiate between correct and incorrect punctuation and spelling',
                        'Identify various grammatical patterns in use',
                        'Interpret information conveyed in sentences',
                    ],
                ],
                [
                    'name'      => 'Oral Forms',
                    'order'     => 3,
                    'subtopics' => [
                        'Vowels: monothongs and diphthongs',
                        'Consonants including clusters',
                        'Rhymes including homophones',
                        'Word stress: monosyllabic and polysyllabic',
                        'Intonation and emphatic stress',
                    ],
                    'objectives' => [
                        'Make distinctions between vowel types',
                        'Differentiate between consonant types',
                        'Identify correct accentuation in individual words and connected speech',
                    ],
                ],
            ],

            // ================================================================
            // ECONOMICS (subject_id: 13)
            // ================================================================
            13 => [
                [
                    'name'      => 'Basic Concepts in Economics',
                    'order'     => 1,
                    'subtopics' => [
                        'Wants, scarcity, choice, scale of preference, opportunity cost',
                        'Rationality, production, distribution, consumption',
                        'Economic problems: what, how and for whom to produce',
                        'Efficiency of resource use',
                        'Production Possibility Frontier (PPF)',
                    ],
                    'objectives' => [
                        'Compare various concepts in economics and their applications',
                        'Interpret graphs and schedules in relation to economic concepts',
                        'Identify economic problems and proffer solutions',
                        'Apply PPF to solution of economic problems',
                    ],
                ],
                [
                    'name'      => 'Economic Systems',
                    'order'     => 2,
                    'subtopics' => [
                        'Types: free enterprise, centrally planned and mixed economies',
                        'Solutions to economic problems under different systems',
                        'Contemporary issues: deregulation, banking sector consolidation, cash policy reform',
                    ],
                    'objectives' => [
                        'Compare the various economic systems',
                        'Apply knowledge of economic systems to contemporary issues in Nigeria',
                        'Proffer solutions to economic problems in different systems',
                    ],
                ],
                [
                    'name'      => 'Methods and Tools of Economic Analysis',
                    'order'     => 3,
                    'subtopics' => [
                        'Inductive and deductive methods',
                        'Positive and normative reasoning',
                        'Tables, charts and graphs',
                        'Measures of central tendency: mean, median and mode',
                        'Measures of dispersion: variance, standard deviation, range',
                    ],
                    'objectives' => [
                        'Distinguish between various forms of reasoning',
                        'Use tools to interpret and analyse economic data',
                        'Assess merits and demerits of analytical tools',
                    ],
                ],
                [
                    'name'      => 'Theory of Demand',
                    'order'     => 4,
                    'subtopics' => [
                        'Meaning and determinants of demand',
                        'Demand schedules and curves',
                        'Change in quantity demanded vs change in demand',
                        'Types of demand: composite, derived, competitive and joint',
                        'Price, income and cross elasticity of demand',
                        'Importance of elasticity to consumers, producers and government',
                    ],
                    'objectives' => [
                        'Identify factors determining demand',
                        'Interpret demand curves from demand schedules',
                        'Differentiate between change in quantity demanded and change in demand',
                        'Compute elasticities and interpret coefficients',
                    ],
                ],
                [
                    'name'      => 'Theory of Consumer Behaviour',
                    'order'     => 5,
                    'subtopics' => [
                        'Utility: cardinal, ordinal, total, average and marginal utilities',
                        'Indifference curve and budget line',
                        'Diminishing marginal utility and law of demand',
                        'Consumer equilibrium using indifference curve and marginal analyses',
                        'Income and substitution effects',
                        'Consumer surplus and applications',
                    ],
                    'objectives' => [
                        'Appraise various utility concepts',
                        'Apply law of demand using marginal utility analysis',
                        'Use indifference curve to determine consumer equilibrium',
                        'Apply consumer surplus to real-life situations',
                    ],
                ],
                [
                    'name'      => 'Theory of Supply',
                    'order'     => 6,
                    'subtopics' => [
                        'Meaning and determinants of supply',
                        'Supply schedules and supply curves',
                        'Change in quantity supplied vs change in supply',
                        'Types of supply: joint, competitive and composite',
                        'Elasticity of supply: determinants, measurements and applications',
                    ],
                    'objectives' => [
                        'Identify factors determining supply',
                        'Interpret supply curves from supply schedules',
                        'Compute and interpret elasticity of supply coefficients',
                    ],
                ],
                [
                    'name'      => 'Theory of Price Determination',
                    'order'     => 7,
                    'subtopics' => [
                        'Concepts of market and price',
                        'Functions of the price system',
                        'Equilibrium price and quantity in product and factor markets',
                        'Price legislation: minimum and maximum prices and their effects',
                        'Effects of changes in supply and demand on equilibrium',
                    ],
                    'objectives' => [
                        'Explain concepts of market and price',
                        'Evaluate effects of government interference with price system',
                        'Interpret effects of changes in supply and demand on equilibrium',
                    ],
                ],
                [
                    'name'      => 'Theory of Production',
                    'order'     => 8,
                    'subtopics' => [
                        'Meaning and types of production',
                        'Total product, average product, marginal product and law of variable proportion',
                        'Division of labour and specialization',
                        'Internal and external economies of scale',
                        'Production functions and returns to scale',
                        'Producer\'s equilibrium: isoquant-isocost analysis',
                        'Factors affecting productivity',
                    ],
                    'objectives' => [
                        'Relate TP, AP and MP with law of variable proportion',
                        'Compare internal and external economies of scale',
                        'Compare different types of returns to scale',
                        'Determine firm\'s equilibrium using isoquant-isocost analysis',
                    ],
                ],
                [
                    'name'      => 'Theory of Costs and Revenue',
                    'order'     => 9,
                    'subtopics' => [
                        'Cost concepts: fixed, variable, total, average and marginal cost',
                        'Revenue concepts: total, average and marginal revenue',
                        'Accountants\' vs economists\' notions of cost',
                        'Short-run and long-run costs',
                        'Marginal cost and the supply curve of a firm',
                    ],
                    'objectives' => [
                        'Explain various cost and revenue concepts',
                        'Differentiate between accountants\' and economists\' notions of costs',
                        'Interpret short-run and long-run cost curves',
                        'Establish relationship between marginal cost and supply curve',
                    ],
                ],
                [
                    'name'      => 'Market Structures',
                    'order'     => 10,
                    'subtopics' => [
                        'Perfect competition: assumptions, characteristics, short-run and long-run equilibrium',
                        'Pure monopoly, discriminatory monopoly and monopolistic competition',
                        'Short-run and long-run equilibrium of imperfect markets',
                        'Break-even and shut-down analysis',
                    ],
                    'objectives' => [
                        'Analyse assumptions and characteristics of perfectly competitive market',
                        'Analyse characteristics of imperfect markets',
                        'Establish conditions for break-even and shut-down of firms',
                    ],
                ],
                [
                    'name'      => 'National Income',
                    'order'     => 11,
                    'subtopics' => [
                        'Concepts of GNP, GDP, NI, NNP',
                        'National income measurements and problems',
                        'Uses and limitations of national income estimates',
                        'Circular flow of income: two and three-sector models',
                        'Consumption, investment and savings',
                        'The multiplier and its effects',
                        'Elementary theory of income determination and equilibrium national income',
                    ],
                    'objectives' => [
                        'Compare different ways of measuring national income',
                        'Interpret circular flow of income',
                        'Calculate various multipliers and evaluate their effects',
                        'Explain concepts of consumption, investment and savings',
                    ],
                ],
                [
                    'name'      => 'Money and Inflation',
                    'order'     => 12,
                    'subtopics' => [
                        'Types, characteristics and functions of money',
                        'Demand and supply of money',
                        'Quantity theory of money: Fisher equation',
                        'Value of money and price level',
                        'Inflation: types, measurements, effects and control',
                        'Deflation: measurements, effects and control',
                        'Consumer price index calculation and interpretation',
                    ],
                    'objectives' => [
                        'Explain types, characteristics and functions of money',
                        'Examine relationship between value of money and price level',
                        'Examine causes and effects of inflation',
                        'Calculate and interpret consumer price index',
                        'Examine ways of controlling inflation and deflation',
                    ],
                ],
                [
                    'name'      => 'Financial Institutions',
                    'order'     => 13,
                    'subtopics' => [
                        'Types and functions: central bank, mortgage banks, merchant banks, insurance companies',
                        'Role of financial institutions in economic development',
                        'Money and capital markets',
                        'Financial sector regulations',
                        'Deposit money banks and creation of money',
                        'Monetary policy and its instruments',
                        'Challenges facing financial institutions in Nigeria',
                    ],
                    'objectives' => [
                        'Identify types and functions of financial institutions',
                        'Distinguish between money and capital markets',
                        'Explain money creation process',
                        'Examine monetary policy instruments and their effects',
                    ],
                ],
                [
                    'name'      => 'Public Finance',
                    'order'     => 14,
                    'subtopics' => [
                        'Meaning and objectives of public finance',
                        'Fiscal policy and its instruments',
                        'Sources of government revenue: taxes, royalties, rents, grants',
                        'Principles and incidence of taxation',
                        'Effects of public expenditure',
                        'Government budget and public debts',
                        'Revenue allocation and resource control in Nigeria',
                    ],
                    'objectives' => [
                        'Identify objectives of public finance',
                        'Analyse principles and incidence of taxation',
                        'Examine types and effects of budgets',
                        'Highlight criteria for revenue allocation in Nigeria',
                    ],
                ],
                [
                    'name'      => 'Economic Growth, Development and Agriculture',
                    'order'     => 15,
                    'subtopics' => [
                        'Meaning and scope of economic growth and development',
                        'Indicators, factors and problems of growth and development in Nigeria',
                        'Development planning in Nigeria',
                        'Types and features of agriculture',
                        'Role of agriculture in economic development',
                        'Problems and policies of agriculture in Nigeria',
                        'Instability in agricultural incomes: causes, effects and solutions',
                    ],
                    'objectives' => [
                        'Distinguish between economic growth and development',
                        'Assess problems of development in Nigeria',
                        'Examine role of agriculture in economic development',
                        'Appraise agricultural policies in Nigeria',
                    ],
                ],
                [
                    'name'      => 'Industry, Natural Resources and Business Organizations',
                    'order'     => 16,
                    'subtopics' => [
                        'Location and localization of industry in Nigeria',
                        'Industrialization strategies and economic development',
                        'Development of major natural resources: petroleum, gold, diamond, timber',
                        'Contributions of oil and non-oil sectors to Nigerian economy',
                        'Upstream and downstream activities, NNPC and OPEC',
                        'Private enterprises: sole proprietorship, partnership, limited liability companies',
                        'Public enterprises, privatization and commercialization',
                    ],
                    'objectives' => [
                        'Differentiate between location and localization of industry',
                        'Assess contributions of oil and non-oil sectors',
                        'Distinguish between upstream and downstream activities in oil sector',
                        'Compare types of private and public business organizations',
                        'Differentiate between privatization and commercialization',
                    ],
                ],
                [
                    'name'      => 'Population, International Trade and Organizations',
                    'order'     => 17,
                    'subtopics' => [
                        'Meaning, theories and census of population',
                        'Over-population, under-population and optimum population',
                        'Population structure, distribution and policy',
                        'Basis for international trade: absolute and comparative costs',
                        'Balance of trade and balance of payments',
                        'Exchange rate: meaning, types and determination',
                        'International organizations: ECOWAS, AU, IMF, World Bank, WTO',
                        'Factors of production: types, features, rewards and theories',
                        'Unemployment: types, causes and solutions in Nigeria',
                    ],
                    'objectives' => [
                        'Analyse population theories and examine relevance to Nigeria',
                        'Examine basis for international trade',
                        'Distinguish between balance of trade and balance of payments',
                        'Identify types of exchange rates',
                        'Evaluate relevance of international organizations to Nigerian economy',
                        'Examine types and causes of unemployment in Nigeria',
                    ],
                ],
            ],

            // ================================================================
            // GOVERNMENT (subject_id: 8)
            // ================================================================
            8 => [
                [
                    'name'      => 'Basic Concepts in Government',
                    'order'     => 1,
                    'subtopics' => [
                        'Power, authority, legitimacy and sovereignty',
                        'Society, state, nation and nation-state',
                        'Political socialization, political participation and political culture',
                    ],
                    'objectives' => [
                        'Identify fundamental concepts in governance',
                        'Analyse various political processes',
                    ],
                ],
                [
                    'name'      => 'Forms and Systems of Government',
                    'order'     => 2,
                    'subtopics' => [
                        'Monarchy, aristocracy, oligarchy, autocracy: definitions, features, merits and demerits',
                        'Republicanism and democracy',
                        'Presidential, parliamentary and monarchical systems',
                        'Unitary, federal and confederal structures',
                    ],
                    'objectives' => [
                        'Distinguish between different forms of government',
                        'Distinguish between different systems of governance',
                        'Compare various political structures of governance',
                    ],
                ],
                [
                    'name'      => 'Arms of Government',
                    'order'     => 3,
                    'subtopics' => [
                        'Legislature: types, structure, functions and powers',
                        'Executive: types, functions and powers',
                        'Judiciary: functions, powers and components',
                        'Relationships among the three arms',
                        'Processes of legislation: acts, edicts, bye-laws, delegated legislation, decrees',
                    ],
                    'objectives' => [
                        'Identify duties and obligations of various arms of government',
                        'Relate each arm to its functions',
                        'Appreciate how arms interrelate',
                        'Analyse processes involved in making of laws',
                    ],
                ],
                [
                    'name'      => 'Political Ideologies, Constitution and Democracy',
                    'order'     => 4,
                    'subtopics' => [
                        'Communalism, feudalism, capitalism, socialism, communism, totalitarianism, fascism, Nazism',
                        'Constitution: meaning, sources, functions and types',
                        'Written, unwritten, rigid and flexible constitutions',
                        'Ethics and accountability, separation of power, checks and balances',
                        'Rule of law, constitutionalism and representative government',
                    ],
                    'objectives' => [
                        'Differentiate between major political ideologies',
                        'Define and identify sources and functions of constitutions',
                        'Identify principles of democratic government',
                    ],
                ],
                [
                    'name'      => 'Citizenship, Electoral Process and Political Parties',
                    'order'     => 5,
                    'subtopics' => [
                        'Citizenship: meaning, types, rights, dual citizenship, renunciation',
                        'Duties and obligations of citizens and the state',
                        'Suffrage: evolution and types',
                        'Election: types and ingredients of free and fair election',
                        'Electoral systems: types, advantages and disadvantages',
                        'Electoral commission: functions and problems',
                        'Political parties: definition, organization and functions',
                        'Party systems: types and characteristics',
                        'Pressure groups: types, functions and differences from political parties',
                        'Public opinion: meaning, formation, measurement, functions and limitations',
                    ],
                    'objectives' => [
                        'Differentiate between methods of acquiring citizenship',
                        'Identify types of electoral systems and analyse electoral processes',
                        'Assess role of political parties',
                        'Evaluate functions of pressure groups',
                        'Compare methods of assessing public opinion',
                    ],
                ],
                [
                    'name'      => 'Civil Service and Pre-Colonial Nigeria',
                    'order'     => 6,
                    'subtopics' => [
                        'Civil service: definition, characteristics, functions, structure, control and problems',
                        'Pre-colonial polities: Hausa, Emirate, Tiv, Igbo, Yoruba',
                        'Structural organization and functions of pre-colonial political institutions',
                    ],
                    'objectives' => [
                        'Analyse significance of civil service in governance',
                        'Appreciate effectiveness of pre-colonial political systems',
                        'Compare pre-colonial systems of governance',
                    ],
                ],
                [
                    'name'      => 'Colonial Rule and Nationalism',
                    'order'     => 7,
                    'subtopics' => [
                        'British process of acquisition: trade, missionary activities, company rule, crown colony',
                        'British colonial policy: direct and indirect rule',
                        'French colonial policy: assimilation and association',
                        'Impact of British colonial rule: economic, political, socio-cultural',
                        'Nationalist leaders: Macaulay, Azikiwe, Awolowo, Ahmadu Bello, Tafawa Balewa',
                        'Emergence of nationalist parties and external factors',
                    ],
                    'objectives' => [
                        'Trace processes of imperialist penetration',
                        'Assess impact of British and French colonial policies',
                        'Evaluate process of decolonization',
                        'Assess roles of nationalist leaders and parties',
                    ],
                ],
                [
                    'name'      => 'Constitutional Development in Nigeria',
                    'order'     => 8,
                    'subtopics' => [
                        'Clifford Constitution 1922',
                        'Richards Constitution 1946',
                        'Macpherson Constitution 1951',
                        'Lyttleton Constitution 1954',
                        'Independence Constitution 1960',
                        'Post-independence constitutions: 1963, 1979, 1989, 1999',
                    ],
                    'objectives' => [
                        'Compare various constitutional developments',
                        'Assess workings of various constitutions',
                    ],
                ],
                [
                    'name'      => 'Nigerian Federalism and Government Institutions',
                    'order'     => 9,
                    'subtopics' => [
                        'Legislature, executive and judiciary in post-independence Nigeria',
                        'Public commissions: Civil Service Commission, Electoral Commissions, National Boundary Commission',
                        'Rationale for federal system and tiers of government',
                        'Creation of states: 1963, 1967, 1976, 1987, 1991, 1996',
                        'Problems of Nigerian federalism: census, revenue allocation, conflicts',
                        'Federal character as corrective measure',
                    ],
                    'objectives' => [
                        'Evaluate operations of arms of government and their agencies',
                        'Examine workings of Nigerian federalism',
                        'Identify and evaluate corrective measures for problems of Nigerian federalism',
                    ],
                ],
                [
                    'name'      => 'Political Parties in Post-Independence Nigeria',
                    'order'     => 10,
                    'subtopics' => [
                        'First Republic political parties',
                        'Second Republic political parties',
                        'Third Republic political parties',
                        'Fourth Republic: evolution, membership and structure',
                    ],
                    'objectives' => [
                        'Contrast political processes in the republics',
                        'Evaluate ideologies, structure and composition of political parties',
                    ],
                ],
                [
                    'name'      => 'Public Corporations, Local Government and Military Rule',
                    'order'     => 11,
                    'subtopics' => [
                        'Public corporations and parastatals: definition, types, purpose, finance and control',
                        'Privatization and commercialization: objectives, features, merits and demerits',
                        'Local government: pre-1976, 1976 and 1989 reforms, structure and functions',
                        'Traditional rulers and local government problems',
                        'Military intervention: factors, structure, impact and disengagement',
                    ],
                    'objectives' => [
                        'Examine operations of public corporations and parastatals',
                        'Trace evolution and structure of local government',
                        'Evaluate reasons for and achievements of military rule',
                    ],
                ],
                [
                    'name'      => 'Nigeria\'s Foreign Policy and International Organizations',
                    'order'     => 12,
                    'subtopics' => [
                        'Foreign policy: definition, purpose, determining factors, formulation and implementation',
                        'Nigeria\'s relations with major powers, developing countries and Non-Alignment Policy',
                        'Africa as centre piece of Nigeria\'s foreign policy and NEPAD',
                        'Nigeria in UN, Commonwealth, OAU, AU, ECOWAS, OPEC',
                        'ECOWAS, OAU/AU, Commonwealth, OPEC: origin, objectives, structure, achievements, problems',
                    ],
                    'objectives' => [
                        'Define foreign policy and identify its determinants',
                        'Identify major objectives of Nigeria\'s foreign policy',
                        'Evaluate role of Nigeria in continental affairs',
                        'Analyse dynamics of Nigeria\'s involvement in international organizations',
                    ],
                ],
            ],

            // ================================================================
            // GEOGRAPHY (subject_id: 11)
            // ================================================================
            11 => [
                [
                    'name'      => 'Maps, Scale and Map Reading',
                    'order'     => 1,
                    'subtopics' => [
                        'Types, uses and definition of maps',
                        'Scale and measurement: distances, areas, reduction and enlargement',
                        'Directions, bearings and gradients',
                        'Cross profiles and intervisibility',
                        'Recognition of physical and human features on topographical maps',
                    ],
                    'objectives' => [
                        'Define and identify different types and uses of maps',
                        'Apply different types of scale to distances and area measurement',
                        'Illustrate relief of an area through profile drawing',
                        'Interpret physical and human features from topographical maps',
                    ],
                ],
                [
                    'name'      => 'Statistical Data, Surveying and GIS',
                    'order'     => 2,
                    'subtopics' => [
                        'Interpretation of statistical data, maps and diagrams',
                        'Chain and prismatic surveying: open and close traverse',
                        'Procedure, problems, advantages and disadvantages of surveying',
                        'GIS: components, techniques, data, sources and applications',
                        'GIS uses: defence, agriculture, rural development',
                    ],
                    'objectives' => [
                        'Compute and interpret quantitative information from statistical data',
                        'Analyse principles and procedure of surveying techniques',
                        'Understand GIS, its components and areas of use',
                    ],
                ],
                [
                    'name'      => 'The Earth as a Planet',
                    'order'     => 3,
                    'subtopics' => [
                        'The earth in the solar system, rotation and revolution',
                        'Shape and size of the earth',
                        'Latitudes and distances',
                        'Longitudes and time',
                    ],
                    'objectives' => [
                        'Identify relative positions of planets in the solar system',
                        'Relate effects of rotation and revolution of the earth',
                        'Differentiate between latitudes and longitudes',
                        'Relate latitude to distance and longitude to time',
                    ],
                ],
                [
                    'name'      => 'The Earth\'s Crust and Landforms',
                    'order'     => 4,
                    'subtopics' => [
                        'Internal and external structure of the earth',
                        'Relationships among atmosphere, biosphere, hydrosphere and lithosphere',
                        'Rocks: types, characteristics, modes of formation and uses',
                        'Tectonic forces: tensional and compressional',
                        'Mountains, plateaux, plains, coastal landforms, karst and desert landforms',
                    ],
                    'objectives' => [
                        'Compare internal and external components of the earth',
                        'Differentiate between major rock types and their characteristics',
                        'Differentiate between tensional and compressional forces',
                        'Identify and describe major landforms',
                    ],
                ],
                [
                    'name'      => 'Volcanism, Earthquakes and Denudation',
                    'order'     => 5,
                    'subtopics' => [
                        'Landforms of volcanic activities and igneous rocks',
                        'Origin and types of volcanoes',
                        'Major volcanic eruptions and earthquakes in the world',
                        'Weathering, erosion, mass movement and deposition',
                        'Agents of denudation and associated landforms',
                    ],
                    'objectives' => [
                        'Explain processes of volcanic eruptions and earthquakes',
                        'Describe landforms associated with volcanic and earthquake activities',
                        'Identify agents of denudation and associate landforms with each process',
                    ],
                ],
                [
                    'name'      => 'Water Bodies',
                    'order'     => 6,
                    'subtopics' => [
                        'Oceans and seas: world distribution, salinity and uses',
                        'Ocean currents: types, distribution, causes and effects',
                        'Lakes: types, distribution and uses',
                        'Rivers: action of running water and landforms of river course stages',
                    ],
                    'objectives' => [
                        'Locate and examine characteristics of oceans and seas',
                        'Classify and account for distribution of ocean currents',
                        'Evaluate causes and effects of ocean currents',
                        'Identify landforms of different stages of a river course',
                    ],
                ],
                [
                    'name'      => 'Weather, Climate and Vegetation',
                    'order'     => 7,
                    'subtopics' => [
                        'Concept, elements and factors controlling weather and climate',
                        'Climate classification: Greek and Koppen',
                        'Major climate types, characteristics and distribution',
                        'Weather measuring instruments',
                        'Climate change: basic science, causes, effects and remedies',
                        'Factors controlling growth of plants',
                        'Major types of vegetation, characteristics and distribution',
                        'Impact of human activities on vegetation',
                    ],
                    'objectives' => [
                        'Differentiate between weather and climate',
                        'Isolate factors controlling weather and climate',
                        'Identify major climate types according to Koppen',
                        'Define climate change and understand its causes and effects',
                        'Identify types of vegetation and their distribution',
                    ],
                ],
                [
                    'name'      => 'Soils and Environmental Issues',
                    'order'     => 8,
                    'subtopics' => [
                        'Soil definition, properties, formation factors and profiles',
                        'Major tropical soil types, characteristics, distribution and uses',
                        'Impact of human activities on soils',
                        'Types of environmental resources: renewable and non-renewable',
                        'Land ecosystem and environmental balance',
                        'Natural hazards: droughts, earthquakes, volcanic eruptions, flooding',
                        'Man-induced hazards: soil erosion, deforestation, pollution, desertification',
                        'Environmental conservation methods and importance',
                    ],
                    'objectives' => [
                        'Classify soils and their properties',
                        'Compare major tropical soil types',
                        'Differentiate between renewable and non-renewable resources',
                        'Identify natural and man-induced hazards and their prevention',
                        'Discuss methods of environmental conservation',
                    ],
                ],
                [
                    'name'      => 'Population and Settlement',
                    'order'     => 9,
                    'subtopics' => [
                        'World population distribution with reference to Amazon Basin, NE USA, India, Japan, West Coast of Southern Africa',
                        'Birth and death rates, age/sex structure',
                        'Factors and patterns of population distribution',
                        'Factors and problems of population growth',
                        'Rural and urban settlements: types, patterns, classification and functions',
                        'Problems of urban centres',
                        'Interrelationship between rural and urban settlements',
                    ],
                    'objectives' => [
                        'Identify characteristics of population',
                        'Determine factors and patterns of population distribution',
                        'Differentiate between types of settlements',
                        'Classify patterns and functions of rural and urban settlements',
                    ],
                ],
                [
                    'name'      => 'Economic Activities and World Trade',
                    'order'     => 10,
                    'subtopics' => [
                        'Types of economic activities: primary, secondary, tertiary and quaternary',
                        'Agriculture: types, systems, factors and problems',
                        'Manufacturing industries: types, locational factors and problems in tropical Africa',
                        'Transportation and communication in tropical Africa',
                        'World trade: factors, patterns, major commodities, routes and destinations',
                        'Tourism: definition, importance, problems and solutions',
                    ],
                    'objectives' => [
                        'Identify and differentiate between types of economic activities',
                        'Assess agriculture as an economic activity',
                        'Identify factors of industrial location',
                        'Relate factors to patterns of world trade',
                        'Analyse tourism as an economic activity',
                    ],
                ],
                [
                    'name'      => 'Regional Geography of Nigeria and ECOWAS',
                    'order'     => 11,
                    'subtopics' => [
                        'Nigeria: location, size, political divisions and peoples',
                        'Physical setting: geology, relief, climate, drainage, vegetation and soils',
                        'Nigerian population: size, distribution and migration',
                        'Natural resources: minerals, soils, water, vegetation',
                        'Agricultural systems and manufacturing industries in Nigeria',
                        'Transportation, communication and trade in Nigeria',
                        'Tourism in Nigeria',
                        'ECOWAS: meaning, objectives, member states, advantages and problems',
                    ],
                    'objectives' => [
                        'Describe location, size and political divisions of Nigeria',
                        'Relate physical settings to human activities in Nigeria',
                        'Identify types and distribution of natural resources in Nigeria',
                        'Compare farming systems in Nigeria',
                        'State meaning, objectives and evaluate prospects of ECOWAS',
                    ],
                ],
            ],

            // ================================================================
            // COMMERCE (subject_id: 15)
            // ================================================================
            15 => [
                [
                    'name'      => 'Introduction to Commerce and Occupation',
                    'order'     => 1,
                    'subtopics' => [
                        'Meaning, scope, characteristics and functions of commerce',
                        'Meaning and importance of occupation',
                        'Types of occupation: industrial, commercial and services',
                        'Factors determining choice of occupation',
                    ],
                    'objectives' => [
                        'Differentiate between commerce and other related subjects',
                        'Describe characteristics and functions of commerce',
                        'Compare different types of occupation',
                    ],
                ],
                [
                    'name'      => 'Production and Factors of Production',
                    'order'     => 2,
                    'subtopics' => [
                        'Factors of production: land, labour, capital and entrepreneur',
                        'Division of labour and specialization',
                        'Types of production: primary, secondary and tertiary',
                    ],
                    'objectives' => [
                        'Identify factors of production and their rewards',
                        'Distinguish between division of labour and specialization',
                        'Classify types of production',
                    ],
                ],
                [
                    'name'      => 'Home and Foreign Trade',
                    'order'     => 3,
                    'subtopics' => [
                        'Retail trade: types, functions, factors, trends and merits/demerits',
                        'Wholesale trade: types, functions and merits/demerits',
                        'Balance of trade, balance of payments and counter trade',
                        'Procedures and documents in export, import and entrepot trade',
                        'Barriers to international trade',
                        'Role of Customs and Excise Authority and Ports Authority',
                    ],
                    'objectives' => [
                        'Compare various types of retailers and wholesalers',
                        'Outline merits and demerits of middlemen',
                        'Analyse basic issues in foreign trade',
                        'Explain procedures and documents used in foreign trade',
                        'Identify barriers to international trade',
                    ],
                ],
                [
                    'name'      => 'Purchase, Sale of Goods and Terms of Trade',
                    'order'     => 4,
                    'subtopics' => [
                        'Procedure and documentation: enquiry, quotation, order, invoice, bill of lading, certificate of origin',
                        'Terms of trade: trade discount, cash discount, COD, CIF, FOB',
                        'Cash payment and legal tender',
                        'Credit: types, functions, merits and demerits',
                    ],
                    'objectives' => [
                        'Examine procedures and documents in purchase and sale of goods',
                        'Determine terms of trade',
                        'Distinguish between cash and credit forms of payment',
                        'Analyse merits and demerits of credit transactions',
                    ],
                ],
                [
                    'name'      => 'Aids to Trade',
                    'order'     => 5,
                    'subtopics' => [
                        'Advertising: types, media, advantages and disadvantages',
                        'Banking: types, services and challenges',
                        'Communication: process, types, trends, merits, demerits and barriers',
                        'Insurance: types, principles, terms and importance',
                        'Tourism: importance, agencies and challenges in Nigeria',
                        'Transportation: modes, importance, advantages and disadvantages',
                        'Warehousing: importance, types, functions and siting factors',
                    ],
                    'objectives' => [
                        'Identify types of advertising and its media',
                        'Categorize types of banks and assess their services',
                        'Analyse types of communication and their merits and demerits',
                        'Describe types of insurance and apply principles to life situations',
                        'Appraise relevance of various modes of transportation',
                        'Highlight importance of warehousing',
                    ],
                ],
                [
                    'name'      => 'Business Units and Financing',
                    'order'     => 6,
                    'subtopics' => [
                        'Forms and features: sole proprietorship, partnership, limited liability companies, public corporations, cooperatives',
                        'Registration, mergers, dissolution and liquidation of businesses',
                        'Sources of finance: savings, shares, bonds, loans, debentures, mortgage, bank overdraft',
                        'Types of capital: share capital, authorized, issued, working capital',
                        'Calculation of capital, profits (gross and net) and turnover',
                        'Role of bureau de change',
                    ],
                    'objectives' => [
                        'Identify forms and features of business units',
                        'Differentiate between dissolution and liquidation',
                        'Identify various ways of financing a business',
                        'Compute different forms of capital, profits and turnover',
                    ],
                ],
                [
                    'name'      => 'Trade Associations, Stock Exchange and Money',
                    'order'     => 7,
                    'subtopics' => [
                        'Objectives and functions of trade and manufacturers\' associations',
                        'Objectives and functions of Chambers of Commerce',
                        'Importance and functions of Stock Exchange',
                        'Types of securities: stocks, shares, bonds, debentures',
                        'Procedure of transactions and speculations',
                        'Second-Tier Securities Market',
                        'Evolution, forms, qualities and functions of money',
                    ],
                    'objectives' => [
                        'Discuss objectives and functions of trade associations',
                        'State importance and functions of Stock Exchange',
                        'Identify different securities traded on Stock Exchange',
                        'Discuss origin, forms and functions of money',
                    ],
                ],
                [
                    'name'      => 'Business Management and Marketing',
                    'order'     => 8,
                    'subtopics' => [
                        'Functions of management: planning, organizing, staffing, coordinating, motivating, controlling',
                        'Principles of management: span of control, unity of command, delegation',
                        'Organizational structures: line, line and staff, functional, matrix, committee',
                        'Functional areas: production, marketing, finance and personnel',
                        'Marketing concept and marketing mix: product, price, place and promotion',
                        'Market segmentation, public relations and customer service',
                    ],
                    'objectives' => [
                        'Appraise functions and principles of management',
                        'Identify organizational structures',
                        'Highlight importance and functions of marketing',
                        'Assess elements of marketing mix',
                        'Explain market segmentation',
                    ],
                ],
                [
                    'name'      => 'Legal Aspects of Business and ICT',
                    'order'     => 9,
                    'subtopics' => [
                        'Meaning and validity of a simple contract',
                        'Agency, Sale of Goods Act and Hire Purchase Act',
                        'Contract of employment',
                        'Government regulations: registration, patents, trademarks, copyrights',
                        'Consumer protection: NAFDAC, NDLEA, Consumer Protection Council',
                        'Computer: appreciation, types, functions, merits and demerits',
                        'ICT terms: internet, intranet, email, browsing, LAN',
                        'E-commerce, e-banking and e-business',
                        'Business environment: legal, political, economic, social, technological',
                        'Social responsibility and types of pollution',
                    ],
                    'objectives' => [
                        'Analyse elements and validity of a simple contract',
                        'Assess rights and obligations of employers and employees',
                        'Distinguish between patents, trademarks and copyrights',
                        'Discuss computer appreciation and application',
                        'Evaluate trends in ICT',
                        'Discuss types of business environment and pollution implications',
                    ],
                ],
            ],

        ];

        foreach ($syllabus as $subjectId => $topics) {
            // Clear existing topics for this subject before seeding
            DB::table('topics')->where('subject_id', $subjectId)->delete();

            foreach ($topics as $topic) {
                DB::table('topics')->insert([
                    'subject_id'  => $subjectId,
                    'name'        => $topic['name'],
                    'order'       => $topic['order'],
                    'subtopics'   => json_encode($topic['subtopics']),
                    'objectives'  => json_encode($topic['objectives']),
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }

            $count = count($topics);
            echo "Seeded {$count} topics for subject_id {$subjectId}\n";
        }

        echo "\nDone! English, Economics, Government, Geography and Commerce topics seeded.\n";
    }
}