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
// CRK (subject_id: 9)
// ================================================================
9 => [

    // =========================
    // SECTION A (1–8)
    // =========================
    [
        'name'  => 'The Sovereignty of God',
        'order' => 1,
        'subtopics' => [
            'God as Creator and Controller of the Universe (Gen. 1–2; cf. Amos 9:5–6; Isa. 45:5–12; Ps. 19:1–6)',
        ],
        'objectives' => [
            'Define the term “sovereignty”',
            "Analyse God's process of creation",
            'Interpret the sequence of creation',
            "Identify man's role in advancing God's purpose in creation",
        ],
    ],
    [
        'name'  => 'The Covenant',
        'order' => 2,
        'subtopics' => [
            "The flood and God's covenant with Noah (Gen. 6:1–22; 7:1–24; 9:1–17)",
            "God's covenant with Abraham (Gen. 11:31–32; 12:1–9; 17:1–21; 21:1–13; 25:19–26)",
            "God's covenant with Israel (Ex. 19–20; 24:1–11; cf. Deut. 28:1–19)",
            "The New Covenant (Jer. 31:31–34; Ezek. 36:25–28)",
        ],
        'objectives' => [
            'Explain the concept of covenant',
            'Examine the importance and implication of the covenants',
            "Distinguish between God's covenants with Noah, Abraham and Israel",
            'Distinguish between the old and the new covenants',
        ],
    ],
    [
        'name'  => 'Leadership Qualities',
        'order' => 3,
        'subtopics' => [
            'Joseph (Gen. 37:1–28; 41:1–57; 45:1–15)',
            'Moses (Ex. 1–5; 12; Num. 13:1–20; 14:1–19)',
            'Joshua (Num. 13:21–33; 27:15–23; Josh. 1:1–15; 6; 7; 24:1–31)',
            'Judges: Deborah (Judg. 4:1–24), Gideon (Judg. 6:11–40), Samson (Judg. 13:1–7, 21–25; 16:4–31)',
        ],
        'objectives' => [
            'Examine the circumstances that gave rise to the leadership of Joseph, Moses, Joshua and the Judges',
            'Identify the major talents of these leaders',
            "Assess God's role in the works of these leaders",
            'Analyse the achievements of these leaders',
        ],
    ],
    [
        'name'  => 'Divine Providence, Guidance and Protection',
        'order' => 4,
        'subtopics' => [
            'Guidance and protection (Gen. 24:1–61; 28:10–22; 46:1–7; Ex. 13:17–22; 14:1–4, 10–31)',
            'Provision (Gen. 21:14–18; 22:1–14; Ex. 16:1–21; 17:1–7; Num. 20:1–13; 1 Kings 17:1–16)',
        ],
        'objectives' => [
            'Identify different ways by which God guided and protected Israel',
            'Specify how God provided for His people',
            'Identify different occasions when God provided for Israel',
        ],
    ],
    [
        'name'  => 'Parental Responsibility',
        'order' => 5,
        'subtopics' => [
            'Eli and Samuel (1 Sam. 2:11–36; 3:2–18; 4:10–22; 8:1–5)',
            'David (2 Sam. 13; 15:1–29; 18; 19:1–8)',
            'Asa (1 Kings 15:9–15; 22:41–44; cf. Deut. 6:4–9; Prov. 4:1–10; 13:1,24; 22:6; 23:13–14; 31:10–31)',
        ],
        'objectives' => [
            'Determine the extent to which Eli, Samuel and David were responsible for the shortcomings of their children',
            'Describe how Asa pleased God',
        ],
    ],
    [
        'name'  => 'Obedience and Disobedience',
        'order' => 6,
        'subtopics' => [
            'Obedience and rewards: Abraham (Gen. 22:1–19), Hebrew midwives (Ex. 1:8–22), David (1 Sam. 30:1–20)',
            'Disobedience and consequences: Adam (Gen. 2:15–25; 3), collection of manna (Ex. 16:22–30), golden calf (Ex. 32), Moses (Num. 20:7–12; Deut. 34:1–6), Saul (1 Sam. 10:1–16; 15:1–25; 16:14–23; 31:1–13)',
        ],
        'objectives' => [
            'Determine why Abraham, the Hebrew midwives and David obeyed God',
            'Identify the rewards for obedience',
            'Compare the disobedience of Adam, the people of Israel, Moses and Saul',
            'Indicate the reasons for their disobedience',
            'Identify the consequences of disobedience',
        ],
    ],
    [
        'name'  => "A Man After God's Own Heart",
        'order' => 7,
        'subtopics' => [
            "Early life of David (1 Sam. 16:1–13; 17; 18:17–30; 22:1–5; 24:1–23; 2 Sam. 2:1–7; 3:1–39)",
            "David's submission to the will of God (1 Sam. 26:1–25; 2 Sam. 12:15–25)",
            "David's repentance and forgiveness (2 Sam. 11; 12:1–15; cf. Ps. 51)",
        ],
        'objectives' => [
            "Identify David's childhood experiences",
            "Specify how David submitted to the will of God",
            "Examine situations that led to David's sin and repentance",
            'Identify why God forgave David',
        ],
    ],
    [
        'name'  => 'Decision-Making',
        'order' => 8,
        'subtopics' => [
            'Reliance on a medium (1 Sam. 28:3–25)',
            'Wisdom of Solomon (1 Kings 3:3–28; 4:29–34; 5:1–12; 8:1–53)',
            "Unwise policies of Solomon and Rehoboam (1 Kings 9:15–23; 11:1–40; 12:1–20)",
        ],
        'objectives' => [
            "Identify the source of Solomon's wisdom",
            'Compare ways used by Saul and Solomon in making decisions',
            "Analyse decisions made by Saul, Solomon and Rehoboam",
            "Assess consequences of Solomon and Rehoboam's unwise decisions",
        ],
    ],

    // =========================
    // SECTION B (9–17)
    // =========================
    [
        'name'  => 'Greed and Its Effects',
        'order' => 9,
        'subtopics' => [
            'Ahab (1 Kings 21:1–29; 22:1–40; 2 Kings 9:30–37)',
            'Gehazi (2 Kings 5:1–27; cf. Josh. 7)',
        ],
        'objectives' => [
            'Deduce the meaning of greed',
            "Distinguish between Ahab and Gehazi's greed",
            "Analyse consequences of Ahab and Gehazi's greed",
        ],
    ],
    [
        'name'  => 'The Supremacy of God (Mount Carmel)',
        'order' => 10,
        'subtopics' => [
            'Religious tension and the power of God on Mount Carmel (1 Kings 16:29–34; 17:1–7; 18; 19:1–18)',
        ],
        'objectives' => [
            'Assess religious situation in Israel at the time of Elijah and Ahab',
            'Identify the characters involved in the contest on Mount Carmel',
            "Differentiate between God's power and that of Baal",
        ],
    ],
    [
        'name'  => 'Religious Reforms in Judah',
        'order' => 11,
        'subtopics' => [
            'Cleansing of the Temple (2 Kings 22)',
            'Renewal of the Covenant (2 Kings 23:1–30)',
        ],
        'objectives' => [
            "Analyse Josiah's religious reforms",
            'Determine reasons for renewal of the covenant',
            'Assess significance of the reforms',
        ],
    ],
    [
        'name'  => 'Concern for Judah',
        'order' => 12,
        'subtopics' => [
            'Fall of Jerusalem (2 Kings 24; 25:1–17)',
            'Condition of Judah (Neh. 1:1–11; Ezra 1:1–11)',
            'Response to the state of Judah (Neh. 2; 4:1–23; Ezra 3:4; 5; 6; 7)',
        ],
        'objectives' => [
            'Identify reasons for the fall of Jerusalem',
            'Examine condition of Judah during the exile',
            "Analyse people's response to Nehemiah and Ezra to rebuild Jerusalem",
            "Distinguish between Nehemiah and Ezra's responses to opposition",
        ],
    ],
    [
        'name'  => 'Faith, Courage and Protection',
        'order' => 13,
        'subtopics' => [
            'Daniel, Shadrach, Meshach and Abednego (Dan. 1; 3:1–30; 6:1–28)',
        ],
        'objectives' => [
            'Analyse the stories of Shadrach, Meshach, Abednego and Daniel',
            'Determine occasions in which the four men demonstrated faith',
            'Analyse effects of their faith on the Babylonians',
        ],
    ],
    [
        'name'  => "God's Message to Nineveh",
        'order' => 14,
        'subtopics' => [
            "Jonah and his message (Jonah 1–4)",
        ],
        'objectives' => [
            "Analyse the story of Jonah's call",
            "Describe consequences of Jonah's disobedience",
            "Assess effect of Jonah's message on the Ninevites",
            'Emulate the example of the Ninevites',
        ],
    ],
    [
        'name'  => 'Social Justice, True Religion and Divine Love',
        'order' => 15,
        'subtopics' => [
            'Social justice and true religion (Amos 2:6–8; 4; 5:1–25; 6:1–14; 7:10–17; 8:4–14; cf. James 1:19–27)',
            'Divine love and human response (Hosea 1–4; 6:1–11; 14)',
        ],
        'objectives' => [
            'Determine what true religion is',
            "Identify the ills that led to the call for social justice in Amos' time",
            "Examine the condition in Israel during Hosea's time",
            "Analyse Hosea's portrayal of divine love and human response",
        ],
    ],
    [
        'name'  => 'Holiness and Divine Call',
        'order' => 16,
        'subtopics' => [
            'Calls of Isaiah, Ezekiel and Jeremiah (Isa. 6:1–13; Ezek. 2; 3:1–11; Jer. 1:4–10)',
        ],
        'objectives' => [
            'Distinguish the calls of Isaiah, Ezekiel and Jeremiah',
            'Compare assignments given to these prophets',
            'Determine the need for God’s people to be holy',
        ],
    ],
    [
        'name'  => 'Punishment and Hope',
        'order' => 17,
        'subtopics' => [
            'Punishment and hope (Jer. 3:11–18; 32:26–35; Ezek. 18; 37:1–14; Isa. 61)',
        ],
        'objectives' => [
            'Describe situations that led to the punishment of Israel',
            'Identify conditions for hope',
            'Determine the benefits of restoration',
        ],
    ],

    // =========================
    // SECTION C (18–36)
    // =========================
    [
        'name'  => 'The Birth and Early Life of Jesus',
        'order' => 18,
        'subtopics' => [
            'John, the forerunner of Jesus (Luke 1:5–25, 57–66; 3:1–20; 7:18–35; Mark 1:1–8; 6:14–29; Matt. 3:1–12; 11:2–19; John 1:6–8, 19–37; 3:22–36)',
            'Birth and boyhood of Jesus (Matt. 1:18–25; 2; Luke 1:26–45; 2)',
        ],
        'objectives' => [
            'Compare stories of the births of John and Jesus',
            'Assess importance of John as the forerunner of Jesus',
            'Describe the boyhood of Jesus',
        ],
    ],
    [
        'name'  => 'The Baptism and Temptation of Jesus',
        'order' => 19,
        'subtopics' => [
            'Baptism and temptation (Matt. 3:13–17; 4:1–11; Mark 1:9–13; Luke 3:21–22; 4:1–13)',
        ],
        'objectives' => [
            'Determine meaning and purpose of the baptism of Jesus',
            'Enumerate the temptations of Jesus',
            'Examine significance of the temptations of Jesus',
        ],
    ],
    [
        'name'  => 'Discipleship',
        'order' => 20,
        'subtopics' => [
            'Call of the first disciples (Matt. 4:18–22; 9:9–13; Mark 1:16–20; 2:13–17; Luke 5:1–11, 27–32)',
            'Demands of discipleship (Matt. 8:19–22; Luke 9:57–63; 14:25–33)',
        ],
        'objectives' => [
            'Identify the first disciples called by Jesus',
            'Determine the demands of discipleship',
        ],
    ],
    [
        'name'  => 'Miracles of Jesus',
        'order' => 21,
        'subtopics' => [
            'Nature miracles: stilling the storm; feeding the five thousand; walking on the sea; changing water to wine',
            'Resuscitation: Lazarus; Jairus’ daughter; widow’s son at Nain',
            'Healing: lepers; paralytic at the pool; centurion’s servant; the blind',
            'Exorcism: Gerasene demoniac; epileptic boy',
        ],
        'objectives' => [
            'Classify different miracles of Jesus',
            'Indicate the occasion of each miracle',
            'Examine significance of each miracle',
        ],
    ],
    [
        'name'  => 'Parables of Jesus',
        'order' => 22,
        'subtopics' => [
            'Parables of the Kingdom: sower; weeds; drag-net; wedding garment',
            'Parables about love of God',
            'Parables about love for one another',
            'Parable about wealth: rich fool',
            'Parables on prayer',
        ],
        'objectives' => [
            'Classify different parables of Jesus',
            'Identify the occasion of each parable',
            'Interpret meaning of each parable',
            'Give reasons why Jesus taught in parables',
        ],
    ],
    [
        'name'  => 'Sermon on the Mount',
        'order' => 23,
        'subtopics' => [
            'Teachings on the Mount (Matt. 5–6; Luke 6:17–26)',
        ],
        'objectives' => [
            'Analyse the teachings on the Mount',
            'Identify the demands of the Kingdom',
            'Determine consequences of worldly possessions',
            'Associate rewards for obedience with the Sermon on the Mount',
        ],
    ],
    [
        'name'  => 'Mission of the Disciples',
        'order' => 24,
        'subtopics' => [
            'Mission of the twelve (Matt. 10:5–15; Mark 6:7–13; Luke 9:1–16)',
            'Mission of the seventy (Luke 10:1–24)',
        ],
        'objectives' => [
            'Distinguish between the mission of the twelve and the seventy',
            'Specify instructions to the disciples',
            'Assess outcomes of the missions',
        ],
    ],
    [
        'name'  => 'The Great Confession',
        'order' => 25,
        'subtopics' => [
            'Peter’s confession (Matt. 16:13–20; Mark 8:27–30; Luke 9:18–22)',
        ],
        'objectives' => [
            'Analyse the confession by Peter',
            'Identify the occasion of the Great Confession',
            'Examine significance of the Great Confession',
        ],
    ],
    [
        'name'  => 'The Transfiguration',
        'order' => 26,
        'subtopics' => [
            'Transfiguration (Matt. 17:1–13; Mark 9:2–13; Luke 9:28–36)',
        ],
        'objectives' => [
            'Trace events leading to the Transfiguration',
            'Determine significance of the Transfiguration to the disciples',
            'Identify personalities involved in the Transfiguration',
        ],
    ],
    [
        'name'  => 'Triumphal Entry and Cleansing of the Temple',
        'order' => 27,
        'subtopics' => [
            'Triumphal entry and temple cleansing (Matt. 21:1–17; Mark 11:1–19; Luke 19:29–48)',
        ],
        'objectives' => [
            'Recount the Triumphal Entry and cleansing of the Temple',
            'Determine significance of both events',
            'Examine how temple cleansing caused hostility towards Jesus',
        ],
    ],
    [
        'name'  => 'The Last Supper',
        'order' => 28,
        'subtopics' => [
            'The Last Supper (Matt. 26:17–30; Mark 14:10–26; Luke 22:7–23; John 13:2–38)',
        ],
        'objectives' => [
            'Trace the story of the Last Supper',
            'Evaluate significance of the Last Supper',
        ],
    ],
    [
        'name'  => 'Trials, Crucifixion and Burial of Jesus',
        'order' => 29,
        'subtopics' => [
            'Trials before the High Priest; Pilate; Herod',
            'Crucifixion and burial (Matt. 27:32–66; Luke 23:26–56; Mark 15:16–47; John 19:17–42)',
        ],
        'objectives' => [
            'Analyse different trials of Jesus',
            'Describe crucifixion and burial of Jesus',
            'Deduce lessons of the death of Jesus',
        ],
    ],
    [
        'name'  => 'Resurrection, Appearances and Ascension of Jesus',
        'order' => 30,
        'subtopics' => [
            'Resurrection and ascension (Matt. 28:1–20; Mark 16:1–20; Luke 24:1–53; John 20:1–31; Acts 1:1–11)',
        ],
        'objectives' => [
            'Trace stories of resurrection, appearances and ascension',
            'Compare personalities involved',
            'Analyse relevance of resurrection and ascension',
        ],
    ],
    [
        'name'  => "Jesus' Teachings about Himself",
        'order' => 31,
        'subtopics' => [
            'Bread of Life and Living Water',
            'Light of the World',
            'Door, Lamb and Good Shepherd',
            'True Vine',
            'Resurrection',
        ],
        'objectives' => [
            "Analyse Jesus' different teachings about Himself",
            "Deduce reasons for Jesus' teachings about Himself",
            'Interpret meanings of the symbols used by Jesus',
        ],
    ],
    [
        'name'  => 'Love',
        'order' => 32,
        'subtopics' => [
            "God's love for man (John 3:16–18)",
            'Love for one another (John 13:34–35; 15:12–13; cf. 1 John 4:7–21)',
        ],
        'objectives' => [
            "Describe God's love for man",
            'Specify ways to love one another',
            'Evaluate significance of love',
        ],
    ],
    [
        'name'  => 'Fellowship in the Early Church',
        'order' => 33,
        'subtopics' => [
            'Communal living (Acts 1:15–26; 2:41–47; 4:32–37)',
            'Problems and solutions (Acts 5:1–11; 6:1–6)',
        ],
        'objectives' => [
            'Identify reasons for communal living',
            'Identify problems of communal living and solutions',
            'Examine how communal living helped the growth of the Early Church',
        ],
    ],
    [
        'name'  => 'The Holy Spirit and the Mission of the Church',
        'order' => 34,
        'subtopics' => [
            'Pentecost (Acts 1:8; 2:1–41)',
            'Mission of the Church (Acts 8:4–40)',
        ],
        'objectives' => [
            'Trace the story of Pentecost',
            'Examine significance of the Pentecost experience',
            'Analyse the mission of the Church',
        ],
    ],
    [
        'name'  => 'Opposition to the Gospel Message',
        'order' => 35,
        'subtopics' => [
            'Arrest and imprisonment of Peter and John (Acts 3; 4:1–22; 5:17–42; 12:1–24)',
            'Martyrdom of Stephen (Acts 6:8–15; 7)',
            'Persecution by Saul (Acts 8:1–3; 9:1–2; cf. Gal. 1:11–17)',
            'Persecution of Paul (Acts 16:11–40; 19:23–41; 21:27–36; cf. 2 Cor. 11:23–33)',
        ],
        'objectives' => [
            'Trace story of arrest and imprisonment of Peter and John',
            'Trace events leading to martyrdom of Stephen',
            'Describe role of Saul in persecution of the Church',
            'Evaluate importance of persecution to growth of the Church',
            'Account for persecution of Paul',
        ],
    ],
    [
        'name'  => 'Mission to the Gentiles',
        'order' => 36,
        'subtopics' => [
            'Conversion of Saul (Acts 9:1–30; 22:4–21; 26:9–18)',
            'Conversion of Cornelius (Acts 10:1–48)',
            'Commissioning and mission of Paul (Acts 13; 14:1–20)',
            'Council of Jerusalem (Acts 15:1–35; Gal. 2:1–21)',
        ],
        'objectives' => [
            'Compare conversions of Saul and Cornelius',
            'Analyse commissioning and mission of Paul',
            'Examine main decisions at the Council of Jerusalem',
            'Identify personalities involved at the Council of Jerusalem',
            'Examine relevance of the main decisions at the Council of Jerusalem',
            "Assess Paul's role in the mission to the Gentiles",
        ],
    ],

    // =========================
    // SECTION D (37–52)
    // =========================
    [
        'name'  => 'Justification by Faith',
        'order' => 37,
        'subtopics' => [
            'Justification by faith (Rom. 3:21–24; 5:1–11; 10:1–13)',
        ],
        'objectives' => [
            'Interpret the phrase “justification by faith”',
            'Identify the basic conditions for justification',
            'Determine the fruits of justification',
        ],
    ],
    [
        'name'  => 'The Law and Grace',
        'order' => 38,
        'subtopics' => [
            'Law and grace (Rom. 4:13–25; 5:18–21; Gal. 3:10–14, 19–29)',
        ],
        'objectives' => [
            'Examine purpose and significance of law and grace',
            'Identify the place of the Law among the Jews',
        ],
    ],
    [
        'name'  => 'New Life in Christ',
        'order' => 39,
        'subtopics' => [
            'New life in Christ (Rom. 6:1–4, 12–14; Col. 3:1–17; Gal. 5:16–26; 2 Cor. 5:16–19; 1 Thess. 4:1–8; Rom. 12)',
        ],
        'objectives' => [
            'Describe characteristics of the old life',
            'Analyse the new life in Christ',
            'Identify conditions of the new life',
            'Examine benefits of the new life',
        ],
    ],
    [
        'name'  => 'Christians as Joint Heirs with Christ',
        'order' => 40,
        'subtopics' => [
            'Joint heirs with Christ (Gal. 3:23–29; 4:1–7)',
        ],
        'objectives' => [
            'Describe how Christians are joint heirs with Christ',
            'Indicate benefits of being joint heirs with Christ',
        ],
    ],
    [
        'name'  => 'Humility',
        'order' => 41,
        'subtopics' => [
            'Humility (Phil. 2:1–11; 1 Pet. 5:5–11)',
        ],
        'objectives' => [
            'Determine the meaning of humility',
            'Identify requirements of humility',
            'Identify rewards of humility',
        ],
    ],
    [
        'name'  => 'Forgiveness',
        'order' => 42,
        'subtopics' => [
            'Forgiveness (Philemon; 2 Cor. 2:5–11)',
        ],
        'objectives' => [
            "Analyse Paul's teaching on forgiveness",
            'Assess benefits of forgiveness',
        ],
    ],
    [
        'name'  => 'Spiritual Gifts',
        'order' => 43,
        'subtopics' => [
            'Spiritual gifts (1 Cor. 12; Rom. 12:3–18; 1 Cor. 14)',
        ],
        'objectives' => [
            'Identify different spiritual gifts',
            'Analyse benefits of spiritual gifts to the individual and the church',
        ],
    ],
    [
        'name'  => 'Christian Giving',
        'order' => 44,
        'subtopics' => [
            'Christian giving (Phil. 4:14–20; 2 Cor. 8:1–5; 9; cf. Matt. 6:2–4)',
        ],
        'objectives' => [
            'Interpret the concept of Christian giving',
            'Relate teachings of Paul on Christian giving',
            'Identify importance of Christian giving',
        ],
    ],
    [
        'name'  => 'Civic Responsibility',
        'order' => 45,
        'subtopics' => [
            'Civic responsibility (Rom. 13; 1 Tim. 2:1–4)',
        ],
        'objectives' => [
            'Identify need for obedience to authority',
            'Specify requirements of good'
         

        ]
    ]
]
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