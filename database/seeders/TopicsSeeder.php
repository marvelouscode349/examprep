<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TopicsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        /**
         * Choose behavior:
         * true  => delete existing topics for these subjects then insert fresh
         * false => upsert (safe re-run, no delete)
         */
        $replaceMode = true;

        // Helper to normalize arrays
        $j = fn($arr) => json_encode($arr ?? [], JSON_UNESCAPED_UNICODE);

        $syllabus = [

            // ================================================================
            // LITERATURE IN ENGLISH (subject_id: 7)
            // ================================================================
            7 => [
                [
                    'name'  => 'Drama',
                    'order' => 1,
                    'subtopics' => [
                        'Types: Tragedy, Comedy, Tragicomedy, Melodrama, Farce, Opera, etc.',
                        'Dramatic Techniques: Characterization, Dialogue, Flashback, Mime, Costume, Music/Dance, Décor/Scenery, Acts/Scenes, Soliloquy/Aside, Figures of Speech, etc.',
                        'Interpretation of Prescribed Texts: Theme, Plot, Socio-political context, Setting',
                    ],
                    'objectives' => [
                        'Identify the various types of drama',
                        'Analyse the contents of the various types of drama',
                        'Compare and contrast the features of different dramatic types',
                        'Demonstrate adequate knowledge of dramatic techniques used in each prescribed text',
                        'Differentiate between styles of selected playwrights',
                        'Determine the theme of any prescribed text',
                        'Identify the plot of the play',
                        'Apply the lessons of the play to everyday living',
                        'Identify the spatial and temporal setting of the play',
                    ],
                ],
                [
                    'name'  => 'Prose',
                    'order' => 2,
                    'subtopics' => [
                        'Types: Fiction (Novel, Novella/Novelette, Short story); Non-fiction (Biography, Autobiography, Memoir); Faction (combination of fact and fiction)',
                        'Narrative Techniques/Devices: Point of view (Omniscient/Third Person, First Person); Characterisation (Round, Flat, Foil, Hero, Antihero, etc.); Language',
                        'Textual Analysis: Theme, Plot, Setting (Temporal/Spatial), Socio-political context',
                    ],
                    'objectives' => [
                        'Differentiate between types of prose',
                        'Identify the category that each prescribed text belongs to',
                        'Analyse the components of each type of prose',
                        'Identify the narrative techniques used in each prescribed text',
                        'Determine an author’s narrative style',
                        'Distinguish between one type of character from another',
                        'Determine the thematic pre-occupation of the author of the prescribed text',
                        'Indicate the plot of the novel',
                        'Identify the temporal and spatial setting of the novel',
                        'Relate the prescribed text to real-life situations',
                    ],
                ],
                [
                    'name'  => 'Poetry',
                    'order' => 3,
                    'subtopics' => [
                        'Types: Sonnet, Ode, Lyrics, Elegy, Ballad, Panegyric, Epic, Blank Verse, etc.',
                        'Poetic devices: Structure, Imagery, Sound (Rhyme/Rhythm, Repetition, Pun, Onomatopoeia, etc.), Diction, Persona',
                        'Appreciation: Thematic preoccupation, Socio-political relevance, Style',
                    ],
                    'objectives' => [
                        'Identify different types of poetry',
                        'Compare and contrast the features of different poetic types',
                        'Determine the devices used by various poets',
                        'Show how poetic devices are used for aesthetic effect in each poem',
                        'Deduce the poet’s preoccupation from the poem',
                        'Appraise poetry as an art with moral values',
                        'Apply the lessons from the poem to real-life situations',
                    ],
                ],
                [
                    'name'  => 'General Literary Principles',
                    'order' => 4,
                    'subtopics' => [
                        'Literary terms: foreshadowing, suspense, theatre, monologue, dialogue, soliloquy, symbolism, protagonist, antagonist, figures of speech, satire, stream of consciousness, etc.',
                        'Literary principles: Direct imitation in play; Versification in drama and poetry; Narration of people’s experiences; Achievement of aesthetic value, etc.',
                        'Relationship between literary terms and principles',
                    ],
                    'objectives' => [
                        'Identify literary terms in drama, prose, and poetry',
                        'Identify the general principles of Literature',
                        'Differentiate between literary terms and principles',
                        'Use literary terms appropriately',
                    ],
                ],
                [
                    'name'  => 'Literary Appreciation',
                    'order' => 5,
                    'subtopics' => [
                        'Unseen passages/extracts from Drama, Prose, and Poetry',
                    ],
                    'objectives' => [
                        'Determine literary devices used in a given passage/extract',
                        'Provide a meaningful interpretation of the given passage/extract',
                        'Relate the extract to true life experiences',
                    ],
                ],
            ],

            // ================================================================
            // HISTORY (subject_id: 10)
            // NOTE: Includes topics across all sections/parts you pasted.
            // ================================================================
            10 => [
                // SECTION A: Nigeria Area up to 1800
                [
                    'name'  => 'Land and Peoples of the Nigeria Area (Up to 1800)',
                    'order' => 1,
                    'subtopics' => [
                        'Geographical zones and the people',
                        'The people’s relationship with the environment',
                        'Relations and integration among peoples of different zones',
                    ],
                    'objectives' => [
                        'Identify the geographical zones and the people within them',
                        'Establish the relationship between the people and the environment',
                        'Comprehend the relationships among the various peoples of the Nigeria area',
                    ],
                ],
                [
                    'name'  => 'Early Centres of Civilization',
                    'order' => 2,
                    'subtopics' => [
                        'Nok, Daima, Ife, Benin, Igbo Ukwu and Iwo Eleru',
                        'Monuments and shelter systems (Kuyambana, Durbi-ta-Kusheyi, city walls and palaces)',
                    ],
                    'objectives' => [
                        'Examine the significance of various centres',
                        'Establish the historical significance of monuments such as caves and rocky formations',
                    ],
                ],
                [
                    'name'  => 'Origin and Formation of States in the Nigeria Area',
                    'order' => 3,
                    'subtopics' => [
                        'Central Sudan: Kanuri and Hausa states',
                        'Niger-Benue Valley: Nupe, Jukun, Igala, Idoma, Tiv and Ebira',
                        'Eastern Forest Belt: Igbo and Ibibio',
                        'Western Forest Belt: Yoruba and Edo',
                        'Coastal and Niger-Delta: Efik, Ijo, Itsekiri and Urhobo',
                        'Factors influencing origin and migration',
                        'Social and political organizations',
                        'Inter-state relations, religion, war and peace',
                    ],
                    'objectives' => [
                        'Relate groups of people occupying the various zones to their traditions of origin',
                        'Determine inter-state relations',
                        'Account for social and political organizations',
                    ],
                ],
                [
                    'name'  => 'Economic Activities and Growth of States',
                    'order' => 4,
                    'subtopics' => [
                        'Agriculture: hunting, farming, fishing, animal husbandry and horticulture',
                        'Industries: pottery, salt-making, iron-smelting, blacksmithing, leather-working, wood-carving, cloth-making, dyeing and food processing',
                        'Trade and trade routes: local, regional, long distance (including trans-Sahara trade)',
                        'Expansion of states',
                    ],
                    'objectives' => [
                        'Identify various economic activities of the people',
                        'Differentiate economic activities and specialties of the people',
                        'Relate trade and other economic activities to growth of states',
                    ],
                ],
                [
                    'name'  => 'External Influences (Up to 1800)',
                    'order' => 5,
                    'subtopics' => [
                        'North Africans/Arabs: introduction, spread and impact of Islam; trans-Saharan trade',
                        'Europeans: early European trade with coastal states; trans-Atlantic slave trade (origin, organization and impact)',
                    ],
                    'objectives' => [
                        'Assess impact of contact with North Africa on people and states south of the Sahara',
                        'Examine impact of early European contact with coastal people',
                        'Trace origin, organization and impact of trans-Atlantic slave trade',
                    ],
                ],

                // SECTION B: Nigeria Area 1800 - 1900
                [
                    'name'  => 'The Sokoto Caliphate and Sokoto Jihad (1800–1900)',
                    'order' => 6,
                    'subtopics' => [
                        'Causes and process of the jihad',
                        'Establishment and administration of the caliphate and relations with neighbours',
                        'Achievements and impact of the caliphate',
                        'Collapse of the caliphate',
                    ],
                    'objectives' => [
                        'Examine causes and processes of the Jihad',
                        'Determine factors that led to rise of the caliphate',
                        'Examine administrative set-up and relations with neighbours',
                        'Examine impact of the caliphate',
                        'Trace internal and external factors that led to collapse of the caliphate',
                    ],
                ],
                [
                    'name'  => 'Kanem-Borno (1800–1900)',
                    'order' => 7,
                    'subtopics' => [
                        'Collapse of the Saifawa dynasty',
                        'Borno under the Shehus',
                        'Borno under Rabeh',
                    ],
                    'objectives' => [
                        'Determine factors that led to collapse of the Saifawa dynasty',
                        'Examine Borno under the administration of the Shehus',
                        'Assess the role of Rabeh in Borno’s history',
                    ],
                ],
                [
                    'name'  => 'Yorubaland (1800–1900)',
                    'order' => 8,
                    'subtopics' => [
                        'Fall of the Old Oyo Empire',
                        'Yoruba wars and their impact',
                        'Peace treaty of 1886 and its aftermath',
                    ],
                    'objectives' => [
                        'Examine causes of the fall of Old Oyo',
                        'Examine causes and effects of Yoruba wars',
                        'Assess impact of the 1886 peace treaty',
                    ],
                ],
                [
                    'name'  => 'Benin (1800–1900)',
                    'order' => 9,
                    'subtopics' => [
                        'Internal political development',
                        'Relations with neighbours',
                        'Relations with Europeans',
                    ],
                    'objectives' => [
                        'Examine internal political development',
                        'Examine relations with neighbours',
                        'Assess relationship with Europeans',
                    ],
                ],
                [
                    'name'  => 'Nupe (1800–1900)',
                    'order' => 10,
                    'subtopics' => [
                        'Internal political development',
                        'Relations with neighbours',
                    ],
                    'objectives' => [
                        'Examine Nupe internal political development',
                        'Assess relations with neighbours',
                    ],
                ],
                [
                    'name'  => 'Igbo (1800–1900)',
                    'order' => 11,
                    'subtopics' => [
                        'Internal political development',
                        'Relations with neighbours',
                    ],
                    'objectives' => [
                        'Examine Igbo internal political development',
                        'Assess relations with neighbours',
                    ],
                ],
                [
                    'name'  => 'Efik (1800–1900)',
                    'order' => 12,
                    'subtopics' => [
                        'Internal political development',
                        'Relations with neighbours',
                    ],
                    'objectives' => [
                        'Examine Efik internal political development',
                        'Assess relations with neighbours',
                    ],
                ],
                [
                    'name'  => 'European Penetration and Impact (1800–1900)',
                    'order' => 13,
                    'subtopics' => [
                        'European exploration of the interior',
                        'Suppression of the trans-Atlantic slave trade',
                        'Development of commodity trade and rise of consular authority',
                        'Christian missionary activities',
                        'Activities of trading companies',
                        'Impact of European activities on coast and hinterland',
                    ],
                    'objectives' => [
                        'Examine motive for exploration of the interior',
                        'Give reasons for suppression of the trans-Atlantic slave trade',
                        'Trace development of commodity trade',
                        'Examine missionary and European activities in the area',
                        'Assess activities of European trading companies',
                        'Account for the rise of consular authority',
                    ],
                ],
                [
                    'name'  => 'British Conquest of the Nigeria Area (1800–1900)',
                    'order' => 14,
                    'subtopics' => [
                        'Motives for the conquest',
                        'Methods of the conquest and its result',
                        'Resistance to and aftermath of the conquest',
                    ],
                    'objectives' => [
                        'Determine reasons for the conquest and methods used',
                        'Examine various resistance to the conquest',
                        'Evaluate results and aftermath of the conquest',
                    ],
                ],

                // SECTION C: Nigeria 1900 - 1960
                [
                    'name'  => 'Establishment of Colonial Rule up to 1914',
                    'order' => 15,
                    'subtopics' => [
                        'Administration of the protectorates',
                    ],
                    'objectives' => [
                        'Examine the administrative set-up of the protectorates',
                    ],
                ],
                [
                    'name'  => 'Amalgamation of 1914',
                    'order' => 16,
                    'subtopics' => [
                        'Reasons',
                        'Effects',
                    ],
                    'objectives' => [
                        'Examine reasons for the 1914 Amalgamation and its effects',
                    ],
                ],
                [
                    'name'  => 'Colonial Administration After Amalgamation',
                    'order' => 17,
                    'subtopics' => [
                        'Central administration: Legislative and Executive Councils',
                        'Indirect Rule: reasons, working and effects',
                        'Local administrative institutions: Native Authorities, Native Courts, Native Treasuries',
                        'Resistance to colonial rule: Ekumeku Movement (1898–1911), Satiru uprising (1906), Egba Anti-tax agitation (1918), Aba Women Movement (1929)',
                    ],
                    'objectives' => [
                        'Relate composition of the central administrative set-up to its consequences',
                        'Identify reasons for introduction and workings of indirect rule system',
                        'Assess effects of indirect rule',
                        'Examine local administrative units',
                        'Account for anti-colonial movements and their significance',
                    ],
                ],
                [
                    'name'  => 'The Colonial Economy',
                    'order' => 18,
                    'subtopics' => [
                        'Currency, taxation and forced labour',
                        'Infrastructure (transportation, post and telecommunication)',
                        'Agriculture',
                        'Mining',
                        'Industry',
                        'Commerce',
                        'Banking',
                    ],
                    'objectives' => [
                        'Examine nature of the economy as it affects taxation, currency, infrastructure, agriculture, mining, industry, commerce and banking',
                    ],
                ],
                [
                    'name'  => 'Social Development under Colonial Rule',
                    'order' => 19,
                    'subtopics' => [
                        'Western education',
                        'Urbanization/social integration',
                        'Improvement unions',
                        'Health institutions',
                    ],
                    'objectives' => [
                        'Identify areas of social development under colonial rule',
                        'Examine impact of urbanization on the people',
                        'Examine level of social integration among the people',
                    ],
                ],
                [
                    'name'  => 'Nationalism, Constitutional Developments and Independence',
                    'order' => 20,
                    'subtopics' => [
                        'Rise of nationalist movements',
                        '1922 Clifford Constitution and rise of Nigeria’s first political party',
                        'World War II and agitation for independence',
                        'Richards Constitution of 1946',
                        'Macpherson Constitution of 1951',
                        'Party politics: regionalism, federalism and minorities agitations',
                        'Lyttleton Constitution of 1954',
                        'Constitutional conferences (Lagos 1957, London 1958)',
                        'General elections of 1959 and independence in 1960',
                    ],
                    'objectives' => [
                        'Trace emergence of nationalist movement',
                        'Assess roles of different constitutions in constitutional development',
                        'Examine effect of World War II on agitation for independence and constitutional developments',
                        'Trace development of party politics and its impact on regionalism and minority question',
                        'Examine impact of constitutional conferences',
                        'Determine factors that aided attainment of independence',
                    ],
                ],

                // SECTION D: Nigeria since Independence
                [
                    'name'  => 'Politics of the First Republic and Military Intervention',
                    'order' => 21,
                    'subtopics' => [
                        'Struggle for control of the centre',
                        'Issue of revenue allocation',
                        'Minority question',
                        '1962/63 census controversies',
                        'Action Group crisis and General Elections of 1964/65',
                        'Coup d’etat of January 1966 and Ironsi Regime',
                    ],
                    'objectives' => [
                        'Give reasons behind struggle for control of the centre',
                        'Account for controversies in revenue allocation',
                        'Account for controversies generated by minority question and creation of states',
                        'Account for controversies generated by 1962/63 census',
                        'Examine problems created by Action Group crisis and General Elections of 1964/65',
                        'Assess significance of military intervention and the Ironsi Regime',
                    ],
                ],
                [
                    'name'  => 'The Civil War',
                    'order' => 22,
                    'subtopics' => [
                        'Causes (remote and immediate)',
                        'Course',
                        'Effects',
                    ],
                    'objectives' => [
                        'Examine remote and immediate causes of the war',
                        'Examine the course',
                        'Assess the effects of the war',
                    ],
                ],
                [
                    'name'  => 'The Gowon Regime',
                    'order' => 23,
                    'subtopics' => [],
                    'objectives' => [
                        'Assess the challenges and achievements of the Gowon Regime',
                    ],
                ],
                [
                    'name'  => 'Murtala/Obasanjo Regime',
                    'order' => 24,
                    'subtopics' => [],
                    'objectives' => [
                        'Assess the challenges and achievements of the Murtala/Obasanjo Regime',
                    ],
                ],
                [
                    'name'  => 'The Second Republic',
                    'order' => 25,
                    'subtopics' => [],
                    'objectives' => [
                        'Evaluate the challenges and achievements of the Second Republic',
                    ],
                ],
                [
                    'name'  => 'The Buhari Regime',
                    'order' => 26,
                    'subtopics' => [],
                    'objectives' => [
                        'Assess the challenges and achievements of the Buhari Regime',
                    ],
                ],
                [
                    'name'  => 'The Babangida Regime',
                    'order' => 27,
                    'subtopics' => [],
                    'objectives' => [
                        'Assess the challenges and achievements of the Babangida Regime',
                    ],
                ],
                [
                    'name'  => 'The Interim National Government (ING)',
                    'order' => 28,
                    'subtopics' => [],
                    'objectives' => [
                        'Examine the role and challenges of the Interim National Government',
                    ],
                ],
                [
                    'name'  => 'The Abacha Regime',
                    'order' => 29,
                    'subtopics' => [],
                    'objectives' => [
                        'Assess the challenges and achievements of the Abacha Regime',
                    ],
                ],
                [
                    'name'  => 'Nigeria in International Organizations',
                    'order' => 30,
                    'subtopics' => [
                        'Economic Community of West African States (ECOWAS)',
                        'African Union (AU)',
                        'Commonwealth of Nations',
                        'Organization of Petroleum Exporting Countries (OPEC)',
                        'United Nations Organization',
                        'Role of Nigeria in conflict resolution',
                    ],
                    'objectives' => [
                        'Examine role of Nigeria in ECOWAS',
                        'Assess role of Nigeria in the AU',
                        'Evaluate role of Nigeria in the Commonwealth of Nations',
                        'Assess role of Nigeria in OPEC',
                        'Examine role of Nigeria in the UN',
                        'Examine role of Nigeria in conflict resolutions in Congo, Chad, Liberia, Sierra Leone, Guinea and Sudan',
                    ],
                ],

                // PART II: Africa and the Wider World since 1800
                [
                    'name'  => 'Islamic Reform Movements and State Building in West Africa',
                    'order' => 31,
                    'subtopics' => [
                        'Relationship between Sokoto and other Jihads',
                        'Jihads of Seku Ahmadu and Al-Hajj Umar',
                        'Activities of Samori Toure',
                    ],
                    'objectives' => [
                        'Establish relationship between Sokoto Jihad and other Jihads in West Africa',
                        'Compare achievements of the Jihads of Seku Ahmadu and Al-Hajj Umar',
                        'Examine activities of Samori Toure of the Madinka Empire',
                    ],
                ],
                [
                    'name'  => 'Sierra Leone, Liberia and Christian Missionary Activities in West Africa',
                    'order' => 32,
                    'subtopics' => [
                        'Foundation of Sierra Leone and Liberia and spread of Christianity',
                        'Activities and impact of Christian missionaries',
                    ],
                    'objectives' => [
                        'Determine factors that led to founding of Sierra Leone and Liberia',
                        'Examine importance of Sierra Leone and Liberia in the spread and impact of Christianity in West Africa',
                        'Assess impact of Christian missionary activities in West Africa',
                    ],
                ],
                [
                    'name'  => 'Egypt under Mohammed Ali and Khedive Ismail',
                    'order' => 33,
                    'subtopics' => [
                        'Rise of Mohammad Ali and his reforms',
                        'Mohammad Ali’s relations with the Europeans',
                        'Ismail’s fiscal policies',
                        'British occupation of Egypt',
                    ],
                    'objectives' => [
                        'Determine factors that aided Mohammad Ali’s rise and reforms',
                        'Establish relationship between Mohammad Ali’s empire and Europeans',
                        'Account for fiscal policies of Ismail',
                        'Examine reasons for British occupation of Egypt',
                    ],
                ],
                [
                    'name'  => 'Mahdi and Mahdiyya Movement in Sudan',
                    'order' => 34,
                    'subtopics' => [
                        'Causes',
                        'Course',
                        'Consequences',
                    ],
                    'objectives' => [
                        'Examine causes, course and consequences of the Mahdiyya Movement in Sudan',
                    ],
                ],
                [
                    'name'  => 'Omani Empire',
                    'order' => 35,
                    'subtopics' => [
                        'Rise of the Omani Empire',
                        'Commercial and political relations with the coast and hinterland',
                        'Relations with Europeans',
                    ],
                    'objectives' => [
                        'Determine factors that led to rise of the Omani Empire',
                        'Assess establishment of commercial and political relations between the Omani Empire, coast and hinterland',
                        'Examine relationship between the Omani Empire and Europeans',
                    ],
                ],
                [
                    'name'  => 'Ethiopia in the 19th Century',
                    'order' => 36,
                    'subtopics' => [
                        'Rise of Theodore II and attempt at unification of Ethiopia',
                        'Menelik II and Ethiopian independence',
                    ],
                    'objectives' => [
                        'Examine factors that led to rise of Theodore II',
                        'Analyse strategies adopted to achieve Ethiopian unification',
                        'Assess role of Menelik II in maintenance of Ethiopian independence',
                    ],
                ],
                [
                    'name'  => 'The Mfecane',
                    'order' => 37,
                    'subtopics' => [
                        'Rise of the Zulu Nation',
                        'Causes, course and consequences of the Mfecane',
                    ],
                    'objectives' => [
                        'Trace events in Nguniland before the Mfecane',
                        'Determine factors that led to rapid rise of Shaka',
                        'Examine causes, course and consequences of the Mfecane',
                    ],
                ],
                [
                    'name'  => 'The Great Trek',
                    'order' => 38,
                    'subtopics' => [
                        'Frontier wars',
                        'British intervention in Boer-African relations',
                        'Great Trek and its consequences',
                    ],
                    'objectives' => [
                        'Determine factors that led to the frontier wars',
                        'Account for British intervention in Boer-African relations',
                        'Describe nature of the Great Trek',
                        'Examine consequences of the Great Trek',
                    ],
                ],
                [
                    'name'  => 'New Imperialism and European Occupation of Africa',
                    'order' => 39,
                    'subtopics' => [
                        'New Imperialism in Africa',
                        'European scramble for Africa',
                        'Berlin Conference',
                        'Occupation and resistance by Africans',
                    ],
                    'objectives' => [
                        'Assess causes of the New Imperialism',
                        'Examine causes of the scramble',
                        'Account for significance of the Berlin Conference',
                        'Examine African resistance to occupation',
                    ],
                ],
                [
                    'name'  => 'Patterns of Colonial Rule in Africa',
                    'order' => 40,
                    'subtopics' => [
                        'The British',
                        'The French',
                        'The Portuguese',
                        'The Belgians',
                    ],
                    'objectives' => [
                        'Examine and compare patterns of colonial rule by various European powers',
                    ],
                ],
                [
                    'name'  => 'Politics of Decolonization',
                    'order' => 41,
                    'subtopics' => [
                        'Colonial policies and African discontent',
                        'Impact of the two World Wars',
                        'Nationalist activities and emergence of political parties and associations',
                        'Strategies for attaining independence',
                    ],
                    'objectives' => [
                        'Examine policies employed by colonial masters and magnitude of African discontent',
                        'Assess impact of First and Second World Wars on African nationalism',
                        'Determine strategies used in attainment of independence',
                    ],
                ],
                [
                    'name'  => 'Apartheid in South Africa',
                    'order' => 42,
                    'subtopics' => [
                        'Origin of apartheid',
                        'Rise of Afrikaner nationalism',
                        'Enactment of apartheid laws',
                        'Internal reaction and suppression of African nationalist movements',
                        'External reaction: Frontline States, Commonwealth, OAU and UN',
                        'Dismantling of apartheid',
                        'Post-apartheid development',
                    ],
                    'objectives' => [
                        'Trace origin of apartheid in South Africa',
                        'Give reasons for rise of Afrikaner nationalism',
                        'Evaluate apartheid laws',
                        'Relate internal reactions to apartheid to African struggle for majority rule',
                        'Relate contributions of African states and international organizations to fight against apartheid',
                        'Identify steps taken towards dismantling apartheid',
                        'Assess post-apartheid development in South Africa',
                    ],
                ],
                [
                    'name'  => 'Problems of Nation-building in Africa',
                    'order' => 43,
                    'subtopics' => [
                        'Political and economic challenges and constraints',
                        'Physical and environmental challenges',
                        'Ethnic and religious pluralism',
                        'Military intervention and political instability',
                        'Neo-colonialism and under-development',
                        'Boundary disputes and threat to African unity',
                        'Civil wars and refugee problem',
                    ],
                    'objectives' => [
                        'Examine political and economic problems faced in Africa',
                        'Assess effects of natural disasters on Africa',
                        'Determine role of ethnic and religious problems in Africa',
                        'Examine role of the military in African politics',
                        'Examine role of neo-colonialism in Africa',
                        'Assess problems of boundary disputes',
                        'Establish relationship between civil wars and refugee problems in Africa',
                    ],
                ],
            ],

            // ================================================================
            // ACCOUNTING (subject_id: 14)
            // ================================================================
           // ================================================================
// ACCOUNTING (subject_id: 14)
// ================================================================
14 => [
    [
        'name'      => 'Nature and Significance of Accounting',
        'order'     => 1,
        'subtopics' => [
            'Development of accounting (including branches of accounting)',
            'Objectives of bookkeeping and accounting',
            'Users and characteristics of accounting information',
            'Principles, concepts and conventions of accounting (nature, significance and application)',
            'Role of accounting records and information',
        ],
        'objectives' => [
            'Differentiate between bookkeeping and accounting',
            'Use the historical background of bookkeeping and accounting for future development',
            'Apply the right principles, concepts and conventions to solving accounting problems',
            'Examine the role of accounting records and information in decision making',
            'List branches of Accounting such as Cost Accounting, Management Accounting, Auditing, Financial Accounting and Taxation',
        ],
    ],
    [
        'name'      => 'Principles of Double Entry',
        'order'     => 2,
        'subtopics' => [
            'Functions of source documents',
            'Books of original entry',
            'Accounting equation',
            'The ledger and its classifications',
            'Trial balance',
            'Types and treatment of errors and uses of suspense account',
        ],
        'objectives' => [
            'Relate the various source documents to their uses',
            'Relate source documents to the various books of original entry',
            'Determine the effect of changes in elements of accounting equation',
            'Identify the role of double entry and use it to post transactions into various divisions of the ledger',
            'Balance off ledger accounts',
            'Extract a trial balance from balances and determine its uses',
            'Identify various types of errors and their necessary corrections',
            'Create a suspense account',
        ],
    ],
    [
        'name'      => 'Ethics in Accounting',
        'order'     => 3,
        'subtopics' => [
            'Objectives',
            'Qualities of an Accountant',
        ],
        'objectives' => [
            'Use ethics in preparing and presenting Accounting Reports',
            'List qualities of an Accountant such as honesty, integrity, transparency, accountability and fairness',
        ],
    ],
    [
        'name'      => 'Cashbook',
        'order'     => 4,
        'subtopics' => [
            'Columnar cashbooks',
            'Discounts',
            'Petty cashbook and the imprest system',
        ],
        'objectives' => [
            'Determine the cash float',
            'Differentiate between two and three columnar cashbooks and how transactions are recorded in them',
            'Differentiate between trade and cash discounts',
            'Examine the effects of trade and cash discounts in the books of accounts',
            'Identify various petty cash expenses',
        ],
    ],
    [
        'name'      => 'Bank Transactions and Reconciliation Statements',
        'order'     => 5,
        'subtopics' => [
            'Instrument of bank transactions',
            'E-banking system',
            'Causes of discrepancies between cashbook and bank statement',
            'Bank reconciliation statement',
        ],
        'objectives' => [
            'Identify bank documents such as cheques, pay-in-slips, credit and debit cards and their uses',
            'Assess the impact of automated credit system, credit transfers, interbank transfers and direct debit on cash balances',
            'List factors that cause discrepancies between balances of cashbook and bank statements',
            'Prepare adjusted cashbook balance',
            'Prepare bank reconciliation statements',
        ],
    ],
    [
        'name'      => 'The Final Accounts of a Sole Trader',
        'order'     => 6,
        'subtopics' => [
            'Income statement (Trading and profit and loss account)',
            'Statement of financial position (Balance sheet)',
            'Adjustments: provision for bad and doubtful debt',
            'Adjustments: provision for discounts',
            'Adjustments: depreciation (straight-line and reducing balance methods)',
            'Adjustments: accruals and prepayments',
        ],
        'objectives' => [
            'Determine the cost of sales, gross profit and net profit of a sole trader',
            'Identify fixed assets, current assets, long-term liabilities, current liabilities and proprietor’s capital',
            'Compute adjustable items on related expenditure and income in the profit and loss account',
            'Relate adjustable items and their corresponding disclosure in the statement of financial position',
            'Differentiate between bad debts and provision for bad and doubtful debts',
        ],
    ],
    [
        'name'      => 'Stock Valuation',
        'order'     => 7,
        'subtopics' => [
            'Methods of cost determination using FIFO, LIFO and simple average',
            'Advantages and disadvantages of the methods',
            'Importance of stock valuation',
        ],
        'objectives' => [
            'Determine cost of materials issued to production or cost of goods sold using FIFO, LIFO and simple average',
            'Calculate closing stock of materials or finished goods using FIFO, LIFO and simple average',
            'Compare advantages and disadvantages of each method of stock valuation',
            'Determine effects of stock valuation on trading, profits and cost of goods sold',
        ],
    ],
    [
        'name'      => 'Control Accounts and Self-balancing Ledgers',
        'order'     => 8,
        'subtopics' => [
            'Importance of control accounts',
            'Purchases ledger control account',
            'Sales ledger control account',
        ],
        'objectives' => [
            'Determine the importance of control accounts in a business enterprise',
            'Differentiate between sales ledger control account and purchases ledger control account',
            'Identify the individual elements of control accounts',
            'Prepare the control accounts',
        ],
    ],
    [
        'name'      => 'Incomplete Records and Single Entry',
        'order'     => 9,
        'subtopics' => [
            'Conversion of single entry to double entry',
            'Determination of missing figures',
            'Preparation of final accounts from incomplete records',
        ],
        'objectives' => [
            'Determine proprietor’s capital using statement of affairs',
            'Determine the amount of sales, purchases, cash balances, debtors, creditors and expenses by converting single entry to double entry',
            'Use accounting equations and gross profit percentage to determine gross profit or cost of sales',
        ],
    ],
    [
        'name'      => 'Manufacturing Accounts',
        'order'     => 10,
        'subtopics' => [
            'Cost classification',
            'Cost apportionment',
            'Preparation of manufacturing account',
        ],
        'objectives' => [
            'Calculate prime cost, production overhead, production cost and total cost',
            'Determine the basis of apportionment into production, administration, selling and distribution',
        ],
    ],
    [
        'name'      => 'Accounts of Not-For-Profit-Making Organizations',
        'order'     => 11,
        'subtopics' => [
            'Objectives of Not-For-Profit-Making organizations',
            'Receipts and payments account',
            'Income and expenditure account',
            'Statement of financial position (Balance sheet)',
        ],
        'objectives' => [
            'Distinguish between features of Not-for-profit-making organizations',
            'Determine subscription income, subscription in arrears and in advance',
            'Compute cash balances and accumulated funds, surplus and deficit for the period from all sources',
            'Prepare receipts and payments account',
            'Prepare income and expenditure account',
            'Prepare statement of financial position',
        ],
    ],
    [
        'name'      => 'Departmental Accounts',
        'order'     => 12,
        'subtopics' => [
            'Objectives',
            'Apportionment of expenses',
            'Departmental trading and profit and loss account',
        ],
        'objectives' => [
            'Identify reasons for departmental accounts',
            'Determine expenses associated with individual departments',
            'Compute departmental profits or losses',
        ],
    ],
    [
        'name'      => 'Branch Accounts',
        'order'     => 13,
        'subtopics' => [
            'Objectives',
            'Branch accounts in the head office books',
            'Head office account',
            'Reconciliation of branch and head office books',
        ],
        'objectives' => [
            'Determine the reasons for branch accounts',
            'Calculate profits and losses from branches',
            'Determine the sources of differences and reconcile them',
        ],
    ],
    [
        'name'      => 'Joint Venture Accounts',
        'order'     => 14,
        'subtopics' => [
            'Objectives',
            'Personal accounts of venturers',
            'Memorandum joint venture accounts',
        ],
        'objectives' => [
            'Identify the objectives of Joint Venture',
            'Determine the profit or loss of the Joint Venture',
            'Determine the profit or loss of each venture',
        ],
    ],
    [
        'name'      => 'Partnership Accounts',
        'order'     => 15,
        'subtopics' => [
            'Formation of partnership',
            'Profit and loss account',
            'Appropriation account',
            'Partners current and capital accounts',
            'Treatment of goodwill',
            'Admission/retirement of a partner',
            'Dissolution of partnership',
            'Conversion of a partnership to a company',
        ],
        'objectives' => [
            'Determine instruments of partnership formation',
            'Categorize all accounts necessary for partnership',
            'Determine effects of admission and retirement of a partner',
            'Prepare revaluation account',
            'Identify accounts required for dissolution and conversion to a company',
            'Determine partners share of profits or losses',
        ],
    ],
    [
        'name'      => 'Introduction to Company Accounts',
        'order'     => 16,
        'subtopics' => [
            'Formation and classification of companies',
            'Issue of shares and debentures',
            'Final accounts of companies',
            'Interpretation of accounts using ratios',
            'Distinction between capital and revenue reserves',
        ],
        'objectives' => [
            'Differentiate between types of companies',
            'Identify processes and procedures of recording issue of shares and debentures',
            'Compute elements of final accounts of companies',
            'Interpret accounts for decision making using ratios such as current, acid test and stock turnover',
        ],
    ],
    [
        'name'      => 'Public Sector Accounting',
        'order'     => 17,
        'subtopics' => [
            'Comparison of cash and accrual basis of accounting',
            'Sources of government revenue',
            'Capital and recurrent expenditure',
            'Consolidated revenue fund',
            'Statement of assets and liabilities',
            'Responsibilities and powers of: Accountant General, Auditor General, Minister of Finance, Treasurer of local government',
            'Instruments of financial regulation',
        ],
        'objectives' => [
            'Differentiate between public sector accounting and private sector accounting',
            'Identify sources of government revenue',
            'Differentiate between capital and recurrent expenditure',
            'Calculate consolidated revenue fund and determine values of assets and liabilities',
            'Analyse duties of Accountant General, Auditor General, Minister of Finance and Treasurer of local government',
            'Distinguish elements of control in government accounting procedures (virement, warrant, votes, authority to incur expenditure, budget, due process certificate)',
        ],
    ],
    [
        'name'      => 'Information Technology in Accounting',
        'order'     => 18,
        'subtopics' => [
            'Manual and computerized accounting processing system',
            'Processes involved in data processing',
            'Computer hardware and software',
            'Advantages and disadvantages of manual and computerized accounting processing system',
        ],
        'objectives' => [
            'Relate and differentiate between manual and computerized accounting processing system',
            'Identify processes involved in data processing',
            'Relate different components of the computer',
            'Identify advantages and disadvantages of manual and computerized accounting processing system',
        ],
    ],
],
        ];

        foreach ($syllabus as $subjectId => $topics) {

            if ($replaceMode) {
                DB::table('topics')->where('subject_id', $subjectId)->delete();
            }

            foreach ($topics as $topic) {
                $payload = [
                    'subject_id' => $subjectId,
                    'name'       => $topic['name'],
                    'order'      => $topic['order'],
                    'subtopics'  => $j($topic['subtopics'] ?? []),
                    'objectives' => $j($topic['objectives'] ?? []),
                    'updated_at' => $now,
                    'created_at' => $now,
                ];

                if ($replaceMode) {
                    DB::table('topics')->insert($payload);
                } else {
                    DB::table('topics')->updateOrInsert(
                        ['subject_id' => $subjectId, 'order' => $topic['order']],
                        $payload
                    );
                }
            }

            echo "Seeded " . count($topics) . " topics for subject_id {$subjectId}\n";
        }

        echo "\nDone! Seeded Literature (7), History (10), Accounting (14).\n";
    }
}