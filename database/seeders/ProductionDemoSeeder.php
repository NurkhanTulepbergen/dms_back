<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\BuySell\Models\BuySellListing;
use Modules\Dormitory\Models\Building;
use Modules\Dormitory\Models\Room;
use Modules\Finance\Models\Charge;
use Modules\Finance\Models\Payment;
use Modules\Gym\Models\GymMembership;
use Modules\Gym\Models\GymPlan;
use Modules\Gym\Models\GymVisit;
use Modules\News\Models\News;
use Modules\Penalty\Models\Penalty;
use Modules\Penalty\Models\PenaltyEvidence;
use Modules\Penalty\Models\PenaltyRedemption;
use Modules\Penalty\Models\PenaltyRule;
use Modules\Penalty\Notifications\DisciplineLimitReachedNotification;
use Modules\Requests\Models\Document;
use Modules\Requests\Models\RepairRequest;
use Modules\Requests\Models\RepairRequestAttachment;
use Modules\Requests\Models\RequestChangeRoom;
use Modules\Requests\Models\RequestLive;
use Modules\Settlement\Models\Settlement;
use Modules\User\Models\DormStudent;
use Modules\User\Models\SystemNotification;
use Modules\User\Models\User;
use Modules\User\Notifications\SystemBroadcastNotification;

class ProductionDemoSeeder extends Seeder
{
    /** @var array<string, User> */
    private array $users = [];

    /** @var array<string, User> */
    private array $students = [];

    /** @var array<string, GymPlan> */
    private array $gymPlans = [];

    /** @var array<string, PenaltyRule> */
    private array $penaltyRules = [];

    /** @var array<string, Settlement> */
    private array $settlements = [];

    public function run(): void
    {
        $this->call(DormitoryStructureSeeder::class);

        $this->seedBuildings();
        $this->seedUsers();
        $this->seedDormStudents();
        $this->seedGymPlans();
        $this->seedPenaltyRules();
        $this->seedSettlements();
        $this->syncRoomOccupancy();
        $this->seedHousingRequests();
        $this->seedChangeRoomRequests();
        $this->seedRepairRequests();
        $this->seedFinance();
        $this->seedGymMemberships();
        $this->seedPenalties();
        $this->seedBuySellListings();
        $this->seedNews();
        $this->seedSystemNotifications();
        $this->seedInboxNotifications();
    }

    private function seedBuildings(): void
    {
        $buildings = [
            [
                'address' => 'Ислама Каримова, 70 к1',
                'name' => 'UniNest Residence A',
                'total_floors' => 7,
                'latitude' => 43.238912,
                'longitude' => 76.889481,
            ],
            [
                'address' => 'Ислама Каримова, 70 к2',
                'name' => 'UniNest Residence B',
                'total_floors' => 6,
                'latitude' => 43.239167,
                'longitude' => 76.889901,
            ],
            [
                'address' => 'Ислама Каримова, 70 к3',
                'name' => 'UniNest Residence C',
                'total_floors' => 5,
                'latitude' => 43.238574,
                'longitude' => 76.890226,
            ],
        ];

        foreach ($buildings as $building) {
            Building::updateOrCreate(
                ['address' => $building['address']],
                $building
            );
        }
    }

    private function seedUsers(): void
    {
        $users = [
            'admin' => [
                'email' => 'admin@uninest.kz',
                'role' => 'admin',
                'lastname' => 'Система',
                'name' => 'Админ',
                'middlename' => 'UniNest',
                'phone_number' => '+77010000001',
                'uni_id' => 'ADM-0001',
                'gender' => 'male',
                'created_at' => '2025-08-20 09:00:00',
            ],
            'manager' => [
                'email' => 'manager@kbtu.kz',
                'role' => 'manager',
                'lastname' => 'Алимова',
                'name' => 'Дана',
                'middlename' => 'Муратовна',
                'phone_number' => '+77010000002',
                'uni_id' => 'MNG-0001',
                'gender' => 'female',
                'created_at' => '2025-08-20 09:15:00',
            ],
            'dorm_admin' => [
                'email' => 'dorm.admin@kbtu.kz',
                'role' => 'dorm-admin',
                'lastname' => 'Омаров',
                'name' => 'Ержан',
                'middlename' => 'Серикович',
                'phone_number' => '+77010000003',
                'uni_id' => 'DADM-0001',
                'gender' => 'male',
                'created_at' => '2025-08-20 09:30:00',
            ],
            'employee_watch' => [
                'email' => 'watchman@kbtu.kz',
                'role' => 'employee',
                'lastname' => 'Касенова',
                'name' => 'Айгуль',
                'middlename' => 'Нурлановна',
                'phone_number' => '+77010000004',
                'uni_id' => 'EMP-0001',
                'gender' => 'female',
                'created_at' => '2025-08-20 09:45:00',
            ],
            'employee_repair' => [
                'email' => 'repair@kbtu.kz',
                'role' => 'employee',
                'lastname' => 'Садыков',
                'name' => 'Руслан',
                'middlename' => 'Ермекович',
                'phone_number' => '+77010000005',
                'uni_id' => 'EMP-0002',
                'gender' => 'male',
                'created_at' => '2025-08-20 10:00:00',
            ],
            'student_nurkhan' => [
                'email' => 'n.tulepbergen@kbtu.kz',
                'role' => 'student',
                'lastname' => 'Тулепберген',
                'name' => 'Нурхан',
                'middlename' => 'Ерболұлы',
                'phone_number' => '+77072747036',
                'uni_id' => '22B030455',
                'gender' => 'male',
                'created_at' => '2025-08-25 11:00:00',
            ],
            'student_aizhan' => [
                'email' => 'a.saparova@kbtu.kz',
                'role' => 'student',
                'lastname' => 'Сапарова',
                'name' => 'Айжан',
                'middlename' => 'Кайратовна',
                'phone_number' => '+77021112233',
                'uni_id' => '22B030118',
                'gender' => 'female',
                'created_at' => '2025-08-25 11:10:00',
            ],
            'student_yerasyl' => [
                'email' => 'y.karimov@kbtu.kz',
                'role' => 'student',
                'lastname' => 'Каримов',
                'name' => 'Ерасыл',
                'middlename' => 'Алиевич',
                'phone_number' => '+77024445566',
                'uni_id' => '22B030219',
                'gender' => 'male',
                'created_at' => '2025-08-25 11:20:00',
            ],
            'student_madina' => [
                'email' => 'm.esenova@kbtu.kz',
                'role' => 'student',
                'lastname' => 'Есенова',
                'name' => 'Мадина',
                'middlename' => 'Болатовна',
                'phone_number' => '+77025556677',
                'uni_id' => '23B031012',
                'gender' => 'female',
                'created_at' => '2025-08-25 11:30:00',
            ],
            'student_arman' => [
                'email' => 'a.nurgali@kbtu.kz',
                'role' => 'student',
                'lastname' => 'Нургали',
                'name' => 'Арман',
                'middlename' => 'Даниярович',
                'phone_number' => '+77027778899',
                'uni_id' => '23B031144',
                'gender' => 'male',
                'created_at' => '2025-08-25 11:40:00',
            ],
            'student_alina' => [
                'email' => 'a.kim@kbtu.kz',
                'role' => 'student',
                'lastname' => 'Ким',
                'name' => 'Алина',
                'middlename' => 'Викторовна',
                'phone_number' => '+77028889900',
                'uni_id' => '23B031201',
                'gender' => 'female',
                'created_at' => '2025-08-25 11:50:00',
            ],
            'student_beibit' => [
                'email' => 'b.akhmet@kbtu.kz',
                'role' => 'student',
                'lastname' => 'Ахмет',
                'name' => 'Бейбит',
                'middlename' => 'Серикович',
                'phone_number' => '+77029990011',
                'uni_id' => '24B032004',
                'gender' => 'male',
                'created_at' => '2025-08-25 12:00:00',
            ],
            'student_dana' => [
                'email' => 'd.kairat@kbtu.kz',
                'role' => 'student',
                'lastname' => 'Кайрат',
                'name' => 'Дана',
                'middlename' => 'Аманжоловна',
                'phone_number' => '+77023334455',
                'uni_id' => '24B032105',
                'gender' => 'female',
                'created_at' => '2025-08-25 12:10:00',
            ],
            'student_sanzhar' => [
                'email' => 's.zhumabay@kbtu.kz',
                'role' => 'student',
                'lastname' => 'Жумабай',
                'name' => 'Санжар',
                'middlename' => 'Ерланович',
                'phone_number' => '+77024443322',
                'uni_id' => '24B032207',
                'gender' => 'male',
                'created_at' => '2025-08-25 12:20:00',
            ],
            'student_tomiris' => [
                'email' => 't.asanova@kbtu.kz',
                'role' => 'student',
                'lastname' => 'Асанова',
                'name' => 'Томирис',
                'middlename' => 'Ермекқызы',
                'phone_number' => '+77025554433',
                'uni_id' => '24B032310',
                'gender' => 'female',
                'created_at' => '2025-08-25 12:30:00',
            ],
            'student_dias' => [
                'email' => 'd.sultan@kbtu.kz',
                'role' => 'student',
                'lastname' => 'Султан',
                'name' => 'Диас',
                'middlename' => 'Маратович',
                'phone_number' => '+77026667788',
                'uni_id' => '25B033010',
                'gender' => 'male',
                'created_at' => '2025-08-25 12:40:00',
            ],
            'student_kamila' => [
                'email' => 'k.abdilda@kbtu.kz',
                'role' => 'student',
                'lastname' => 'Абдильда',
                'name' => 'Камила',
                'middlename' => 'Нурбековна',
                'phone_number' => '+77027776655',
                'uni_id' => '25B033112',
                'gender' => 'female',
                'created_at' => '2025-08-25 12:50:00',
            ],
        ];

        foreach ($users as $key => $payload) {
            $createdAt = $payload['created_at'];
            unset($payload['created_at']);

            $user = User::updateOrCreate(
                ['email' => $payload['email']],
                array_merge($payload, [
                    'password' => 'password',
                    'discipline_limit' => 10,
                    'email_verified_at' => Carbon::parse($createdAt)->addMinutes(5),
                ])
            );

            $this->stamp($user, $createdAt);

            $this->users[$key] = $user;
            if ($user->role === 'student') {
                $this->students[$key] = $user;
            }
        }
    }

    private function seedDormStudents(): void
    {
        $warnings = [
            'student_nurkhan' => 1,
            'student_aizhan' => 0,
            'student_yerasyl' => 2,
            'student_madina' => 0,
            'student_arman' => 1,
            'student_alina' => 0,
            'student_beibit' => 1,
            'student_dana' => 0,
            'student_sanzhar' => 0,
            'student_tomiris' => 3,
            'student_dias' => 2,
            'student_kamila' => 0,
        ];

        foreach ($warnings as $key => $warningCount) {
            DormStudent::updateOrCreate(
                ['user_id' => $this->students[$key]->id],
                ['warning_count' => $warningCount]
            );
        }
    }

    private function seedGymPlans(): void
    {
        $plans = [
            'trial' => ['name' => 'Start 4', 'total_sessions' => 4, 'price' => 9000, 'duration_days' => 14, 'is_active' => true],
            'standard' => ['name' => 'Student 12', 'total_sessions' => 12, 'price' => 22000, 'duration_days' => 30, 'is_active' => true],
            'premium' => ['name' => 'Active 30', 'total_sessions' => 30, 'price' => 45000, 'duration_days' => 45, 'is_active' => true],
            'archived' => ['name' => 'Spring Promo', 'total_sessions' => 8, 'price' => 15000, 'duration_days' => 21, 'is_active' => false],
        ];

        foreach ($plans as $key => $payload) {
            $this->gymPlans[$key] = GymPlan::updateOrCreate(
                ['name' => $payload['name']],
                $payload
            );
        }
    }

    private function seedPenaltyRules(): void
    {
        $rules = [
            'ROOM_DIRTY' => ['title' => 'Неубранная комната', 'default_points' => 2, 'redeemable' => true],
            'QUIET_HOURS' => ['title' => 'Шум после 23:00', 'default_points' => 4, 'redeemable' => true],
            'LATE_TRASH' => ['title' => 'Несвоевременный вынос мусора', 'default_points' => 1, 'redeemable' => true],
            'GUEST_RULES' => ['title' => 'Нарушение правил проживания гостей', 'default_points' => 3, 'redeemable' => true],
            'SMOKING' => ['title' => 'Курение в запрещенных местах', 'default_points' => 5, 'redeemable' => false],
            'FORBIDDEN_DEVICE' => ['title' => 'Использование запрещенных приборов', 'default_points' => 5, 'redeemable' => false],
            'COMMON_AREA' => ['title' => 'Порча имущества общего пользования', 'default_points' => 3, 'redeemable' => true],
        ];

        foreach ($rules as $code => $payload) {
            $this->penaltyRules[$code] = PenaltyRule::updateOrCreate(
                ['code' => $code],
                array_merge($payload, [
                    'creates_financial_charge' => false,
                    'financial_amount' => null,
                ])
            );
        }
    }

    private function seedSettlements(): void
    {
        $active = [
            'student_nurkhan' => ['room' => ['Ислама Каримова, 70 к1', 705], 'start_at' => '2025-09-01'],
            'student_aizhan' => ['room' => ['Ислама Каримова, 70 к2', 502], 'start_at' => '2025-09-02'],
            'student_yerasyl' => ['room' => ['Ислама Каримова, 70 к3', 404], 'start_at' => '2025-09-02'],
            'student_madina' => ['room' => ['Ислама Каримова, 70 к2', 304], 'start_at' => '2025-09-03'],
            'student_arman' => ['room' => ['Ислама Каримова, 70 к3', 504], 'start_at' => '2025-09-03'],
            'student_alina' => ['room' => ['Ислама Каримова, 70 к2', 601], 'start_at' => '2025-09-04'],
            'student_beibit' => ['room' => ['Ислама Каримова, 70 к1', 302], 'start_at' => '2025-09-04'],
            'student_dana' => ['room' => ['Ислама Каримова, 70 к2', 203], 'start_at' => '2025-09-05'],
            'student_sanzhar' => ['room' => ['Ислама Каримова, 70 к1', 401], 'start_at' => '2025-09-05'],
            'student_dias' => ['room' => ['Ислама Каримова, 70 к3', 103], 'start_at' => '2025-09-06'],
        ];

        foreach ($active as $key => $payload) {
            $room = $this->room($payload['room'][0], $payload['room'][1]);

            $settlement = Settlement::updateOrCreate(
                [
                    'user_id' => $this->students[$key]->id,
                    'end_at' => null,
                ],
                [
                    'room_id' => $room->id,
                    'start_at' => $payload['start_at'],
                    'status' => 'active',
                    'source' => 'admin_manual',
                    'end_reason' => null,
                ]
            );

            $this->settlements[$key] = $settlement;
        }

        $historyRoom = $this->room('Ислама Каримова, 70 к2', 204);
        $this->settlements['student_tomiris'] = Settlement::updateOrCreate(
            [
                'user_id' => $this->students['student_tomiris']->id,
                'source' => 'discipline',
            ],
            [
                'room_id' => $historyRoom->id,
                'start_at' => '2025-09-04',
                'end_at' => '2026-04-18',
                'status' => 'finished',
                'end_reason' => 'eviction',
            ]
        );
    }

    private function syncRoomOccupancy(): void
    {
        Room::query()->update(['live_cap' => 0]);

        $activeCounts = Settlement::query()
            ->select('room_id', DB::raw('count(*) as residents_count'))
            ->whereNull('end_at')
            ->groupBy('room_id')
            ->get();

        foreach ($activeCounts as $row) {
            Room::query()
                ->where('id', $row->room_id)
                ->update(['live_cap' => (int) $row->residents_count]);
        }
    }

    private function seedHousingRequests(): void
    {
        $requests = [
            [
                'key' => 'student_kamila',
                'room' => ['Ислама Каримова, 70 к2', 205],
                'status' => 'pending',
                'created_at' => '2026-06-03 10:20:00',
                'documents' => [
                    ['type' => 'identity_card', 'path' => 'documents/kamila/identity-card.pdf'],
                    ['type' => 'medical_certificate', 'path' => 'documents/kamila/medical-certificate.pdf'],
                ],
            ],
            [
                'key' => 'student_tomiris',
                'room' => ['Ислама Каримова, 70 к2', 305],
                'status' => 'rejected',
                'created_at' => '2026-05-20 14:00:00',
                'documents' => [
                    ['type' => 'identity_card', 'path' => 'documents/tomiris/identity-card.pdf'],
                ],
            ],
            [
                'key' => 'student_nurkhan',
                'room' => ['Ислама Каримова, 70 к1', 705],
                'status' => 'accepted',
                'created_at' => '2025-08-28 12:15:00',
                'documents' => [
                    ['type' => 'identity_card', 'path' => 'documents/nurkhan/identity-card.pdf'],
                    ['type' => 'dorm_contract', 'path' => 'documents/nurkhan/dorm-contract.pdf'],
                ],
            ],
        ];

        foreach ($requests as $payload) {
            $room = $this->room($payload['room'][0], $payload['room'][1]);
            $request = RequestLive::updateOrCreate(
                [
                    'user_id' => $this->students[$payload['key']]->id,
                    'preferred_room_id' => $room->id,
                ],
                ['status' => $payload['status']]
            );

            $this->stamp($request, $payload['created_at']);

            foreach ($payload['documents'] as $document) {
                Document::updateOrCreate(
                    [
                        'request_id' => $request->id,
                        'type' => $document['type'],
                    ],
                    ['path' => $document['path']]
                );
            }
        }
    }

    private function seedChangeRoomRequests(): void
    {
        $requests = [
            [
                'key' => 'student_nurkhan',
                'room' => ['Ислама Каримова, 70 к1', 604],
                'status' => 'pending',
                'description' => 'Хочу переехать ближе к учебной зоне, потому что часто занимаюсь до позднего вечера.',
                'created_at' => '2026-06-05 16:45:00',
            ],
            [
                'key' => 'student_aizhan',
                'room' => ['Ислама Каримова, 70 к2', 503],
                'status' => 'accepted',
                'description' => 'Переезд согласован с соседкой по комнате.',
                'created_at' => '2026-05-18 09:10:00',
            ],
            [
                'key' => 'student_yerasyl',
                'room' => ['Ислама Каримова, 70 к3', 405],
                'status' => 'rejected',
                'description' => 'Желаемая комната занята, прошу подобрать альтернативу.',
                'created_at' => '2026-05-12 13:30:00',
            ],
        ];

        foreach ($requests as $payload) {
            $room = $this->room($payload['room'][0], $payload['room'][1]);
            $request = RequestChangeRoom::updateOrCreate(
                [
                    'student_id' => $this->students[$payload['key']]->id,
                    'status' => $payload['status'],
                ],
                [
                    'room_id' => $room->id,
                    'description' => $payload['description'],
                ]
            );

            $this->stamp($request, $payload['created_at']);
        }
    }

    private function seedRepairRequests(): void
    {
        $employee = $this->users['employee_repair'];

        $requests = [
            [
                'key' => 'student_nurkhan',
                'category' => 'plumbing',
                'title' => 'Протекает кран в ванной',
                'description' => 'После вечернего использования вода продолжает капать, на полу появляется лужа.',
                'status' => 'pending',
                'created_at' => '2026-06-07 08:35:00',
                'attachments' => ['repairs/nurkhan/faucet-1.jpg'],
            ],
            [
                'key' => 'student_madina',
                'category' => 'electricity',
                'title' => 'Не работает розетка у стола',
                'description' => 'Розетка справа от стола перестала работать после скачка напряжения.',
                'status' => 'in_progress',
                'employee_comment' => 'Электрик назначен, ожидаем замену блока розеток.',
                'started_at' => '2026-06-07 14:00:00',
                'created_at' => '2026-06-06 19:20:00',
                'attachments' => ['repairs/madina/socket-1.jpg'],
            ],
            [
                'key' => 'student_arman',
                'category' => 'furniture',
                'title' => 'Сломалась дверца шкафа',
                'description' => 'Петля шкафа расшаталась, дверца не закрывается полностью.',
                'status' => 'resolved',
                'employee_comment' => 'Петля заменена, шкаф проверен.',
                'started_at' => '2026-06-03 11:00:00',
                'resolved_at' => '2026-06-03 16:30:00',
                'created_at' => '2026-06-02 21:10:00',
                'attachments' => ['repairs/arman/wardrobe-1.jpg'],
            ],
            [
                'key' => 'student_aizhan',
                'category' => 'heating',
                'title' => 'Слабое отопление в комнате',
                'description' => 'Комната остается прохладной утром, батарея нагревается неравномерно.',
                'status' => 'resolved',
                'employee_comment' => 'Проведена регулировка вентиля, температура нормализовалась.',
                'started_at' => '2026-05-28 10:00:00',
                'resolved_at' => '2026-05-28 12:15:00',
                'created_at' => '2026-05-27 18:25:00',
                'attachments' => [],
            ],
        ];

        foreach ($requests as $payload) {
            $settlement = $this->settlements[$payload['key']];
            $request = RepairRequest::updateOrCreate(
                [
                    'user_id' => $this->students[$payload['key']]->id,
                    'title' => $payload['title'],
                ],
                [
                    'room_id' => $settlement->room_id,
                    'handled_by_id' => $payload['status'] === 'pending' ? null : $employee->id,
                    'category' => $payload['category'],
                    'description' => $payload['description'],
                    'status' => $payload['status'],
                    'employee_comment' => $payload['employee_comment'] ?? null,
                    'started_at' => $payload['started_at'] ?? null,
                    'resolved_at' => $payload['resolved_at'] ?? null,
                ]
            );

            $this->stamp($request, $payload['created_at']);

            foreach ($payload['attachments'] as $path) {
                RepairRequestAttachment::updateOrCreate(
                    [
                        'repair_request_id' => $request->id,
                        'file_path' => $path,
                    ],
                    []
                );
            }
        }
    }

    private function seedFinance(): void
    {
        $statuses = [
            'student_nurkhan' => ['status' => 'paid', 'paid_at' => '2026-02-04 10:12:00'],
            'student_aizhan' => ['status' => 'pending'],
            'student_yerasyl' => ['status' => 'paid', 'paid_at' => '2026-02-05 11:44:00'],
            'student_madina' => ['status' => 'pending'],
            'student_arman' => ['status' => 'paid', 'paid_at' => '2026-02-06 15:30:00'],
            'student_alina' => ['status' => 'pending'],
            'student_beibit' => ['status' => 'paid', 'paid_at' => '2026-02-07 09:18:00'],
            'student_dana' => ['status' => 'pending'],
        ];

        foreach ($statuses as $key => $state) {
            $settlement = $this->settlements[$key]->load('room.roomType');
            $amount = (float) ($settlement->room->roomType?->semester_price ?? 500000);

            $charge = Charge::updateOrCreate(
                [
                    'settlement_id' => $settlement->id,
                    'type' => 'semester_rent',
                ],
                [
                    'user_id' => $this->students[$key]->id,
                    'gym_plan_id' => null,
                    'amount' => $amount,
                    'currency' => 'KZT',
                    'period_start' => '2026-02-01',
                    'period_end' => '2026-06-30',
                    'status' => $state['status'],
                ]
            );

            if ($state['status'] === 'paid') {
                $this->seedPayment(
                    $charge,
                    'cs_demo_housing_'.$this->students[$key]->uni_id,
                    $state['paid_at']
                );
            }
        }

        $settlement = $this->settlements['student_yerasyl'];
        Charge::updateOrCreate(
            [
                'settlement_id' => $settlement->id,
                'type' => 'penalty_fee',
            ],
            [
                'user_id' => $this->students['student_yerasyl']->id,
                'gym_plan_id' => null,
                'amount' => 15000,
                'currency' => 'KZT',
                'period_start' => '2026-06-01',
                'period_end' => '2026-06-15',
                'status' => 'pending',
            ]
        );
    }

    private function seedGymMemberships(): void
    {
        $memberships = [
            [
                'key' => 'student_nurkhan',
                'plan' => 'standard',
                'status' => 'active',
                'remaining_sessions' => 8,
                'started_at' => '2026-05-27',
                'expires_at' => '2026-06-26',
                'paid_at' => '2026-05-27 12:00:00',
                'visits' => [
                    ['date' => '2026-06-02', 'in' => '18:10:00', 'out' => '19:25:00', 'minutes' => 75],
                    ['date' => '2026-06-04', 'in' => '17:55:00', 'out' => '19:05:00', 'minutes' => 70],
                    ['date' => '2026-06-07', 'in' => '18:30:00', 'out' => '19:40:00', 'minutes' => 70],
                ],
            ],
            [
                'key' => 'student_madina',
                'plan' => 'premium',
                'status' => 'active',
                'remaining_sessions' => 25,
                'started_at' => '2026-05-20',
                'expires_at' => '2026-07-04',
                'paid_at' => '2026-05-20 16:22:00',
                'visits' => [
                    ['date' => '2026-06-01', 'in' => '07:20:00', 'out' => '08:10:00', 'minutes' => 50],
                    ['date' => '2026-06-06', 'in' => '09:00:00', 'out' => '10:05:00', 'minutes' => 65],
                ],
            ],
            [
                'key' => 'student_beibit',
                'plan' => 'trial',
                'status' => 'exhausted',
                'remaining_sessions' => 0,
                'started_at' => '2026-05-10',
                'expires_at' => '2026-05-24',
                'paid_at' => '2026-05-10 13:10:00',
                'visits' => [
                    ['date' => '2026-05-12', 'in' => '19:00:00', 'out' => '20:00:00', 'minutes' => 60],
                    ['date' => '2026-05-14', 'in' => '19:10:00', 'out' => '20:00:00', 'minutes' => 50],
                    ['date' => '2026-05-18', 'in' => '18:40:00', 'out' => '19:45:00', 'minutes' => 65],
                    ['date' => '2026-05-22', 'in' => '18:30:00', 'out' => '19:30:00', 'minutes' => 60],
                ],
            ],
        ];

        foreach ($memberships as $payload) {
            $user = $this->students[$payload['key']];
            $plan = $this->gymPlans[$payload['plan']];

            $charge = Charge::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'gym_plan_id' => $plan->id,
                    'type' => 'gym_membership',
                ],
                [
                    'settlement_id' => null,
                    'amount' => $plan->price,
                    'currency' => 'KZT',
                    'period_start' => $payload['started_at'],
                    'period_end' => $payload['expires_at'],
                    'status' => 'paid',
                ]
            );

            $this->seedPayment($charge, 'cs_demo_gym_'.$user->uni_id.'_'.$plan->id, $payload['paid_at']);

            $membership = GymMembership::updateOrCreate(
                ['charge_id' => $charge->id],
                [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'total_sessions' => $plan->total_sessions,
                    'remaining_sessions' => $payload['remaining_sessions'],
                    'started_at' => $payload['started_at'],
                    'expires_at' => $payload['expires_at'],
                    'status' => $payload['status'],
                ]
            );

            foreach ($payload['visits'] as $visit) {
                $this->upsertGymVisit($membership, $user, $visit);
            }
        }

        $pendingPlan = $this->gymPlans['standard'];
        Charge::updateOrCreate(
            [
                'user_id' => $this->students['student_alina']->id,
                'gym_plan_id' => $pendingPlan->id,
                'type' => 'gym_membership',
            ],
            [
                'settlement_id' => null,
                'amount' => $pendingPlan->price,
                'currency' => 'KZT',
                'period_start' => '2026-06-08',
                'period_end' => '2026-07-08',
                'status' => 'pending',
            ]
        );
    }

    private function seedPenalties(): void
    {
        $penalty = $this->upsertPenalty(
            'student_nurkhan',
            'ROOM_DIRTY',
            2,
            'active',
            'Комната была оставлена неприбранной после плановой проверки.',
            '2026-06-01 12:00:00',
            ['penalties/nurkhan/room-check.jpg']
        );
        $this->upsertRedemption(
            $penalty,
            'Общественная уборка',
            'Выполнил уборку учебной комнаты на 2 этаже, фотоотчет приложен.',
            'pending',
            null,
            '2026-06-06 18:10:00'
        );

        $penalty = $this->upsertPenalty(
            'student_aizhan',
            'LATE_TRASH',
            1,
            'resolved',
            'Мусор был вынесен позже установленного времени.',
            '2026-05-18 09:30:00',
            []
        );
        $this->upsertRedemption(
            $penalty,
            'Помощь на ресепшене',
            'Дежурила на выдаче ключей и помогла обновить журнал посетителей.',
            'approved',
            $this->users['dorm_admin']->id,
            '2026-05-20 16:20:00'
        );

        $this->upsertPenalty(
            'student_yerasyl',
            'QUIET_HOURS',
            4,
            'active',
            'После 23:00 в комнате была громкая музыка, зафиксирована жалоба соседей.',
            '2026-06-04 23:40:00',
            ['penalties/yerasyl/noise-report.pdf']
        );

        $penalty = $this->upsertPenalty(
            'student_madina',
            'GUEST_RULES',
            3,
            'active',
            'Гость находился в общежитии после разрешенного времени без регистрации.',
            '2026-06-02 22:15:00',
            []
        );
        $this->upsertRedemption(
            $penalty,
            'Помощь по общежитию',
            'Прошу зачесть участие в сортировке вещей для благотворительной акции.',
            'rejected',
            $this->users['dorm_admin']->id,
            '2026-06-05 17:00:00'
        );

        $this->upsertPenalty(
            'student_dias',
            'SMOKING',
            5,
            'active',
            'Курение обнаружено в зоне лестничной клетки.',
            '2026-06-07 21:30:00',
            ['penalties/dias/staircase-act.pdf']
        );

        $this->upsertPenalty(
            'student_tomiris',
            'SMOKING',
            5,
            'active',
            'Повторное нарушение правил общежития, акт дежурного администратора.',
            '2026-04-15 20:00:00',
            []
        );
        $this->upsertPenalty(
            'student_tomiris',
            'FORBIDDEN_DEVICE',
            5,
            'active',
            'Использование запрещенного нагревательного прибора в комнате.',
            '2026-04-18 09:00:00',
            []
        );
    }

    private function seedBuySellListings(): void
    {
        $listings = [
            [
                'key' => 'student_nurkhan',
                'title' => 'Зарядка USB-C для MacBook 67W',
                'category' => 'electronics',
                'condition' => 'like_new',
                'price' => 12000,
                'pickup_location' => 'Residence A, 7 этаж',
                'status' => 'active',
                'description' => 'Оригинальная зарядка, пользовался один семестр. Подходит для MacBook Air/Pro с USB-C.',
                'images' => ['https://images.unsplash.com/photo-1581090464777-f3220bbe1b8b?auto=format&fit=crop&w=900&q=80'],
                'published_at' => '2026-06-01 12:00:00',
            ],
            [
                'key' => 'student_aizhan',
                'title' => 'Calculus: Early Transcendentals',
                'category' => 'textbooks',
                'condition' => 'good',
                'price' => 8500,
                'pickup_location' => 'Residence B, ресепшен',
                'status' => 'active',
                'description' => 'Учебник для Calculus I-II, есть аккуратные пометки карандашом.',
                'images' => ['https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=900&q=80'],
                'published_at' => '2026-06-02 17:30:00',
            ],
            [
                'key' => 'student_madina',
                'title' => 'Настольная лампа Xiaomi',
                'category' => 'home',
                'condition' => 'good',
                'price' => 7000,
                'pickup_location' => 'Residence B, 3 этаж',
                'status' => 'active',
                'description' => 'Лампа с регулировкой яркости, отлично подходит для учебного стола.',
                'images' => ['https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=900&q=80'],
                'published_at' => '2026-06-03 11:45:00',
            ],
            [
                'key' => 'student_arman',
                'title' => 'Зимняя куртка Columbia',
                'category' => 'clothing',
                'condition' => 'fair',
                'price' => 18000,
                'pickup_location' => 'Residence C, 5 этаж',
                'status' => 'sold',
                'description' => 'Теплая куртка, размер M. Есть небольшие следы использования.',
                'images' => ['https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=900&q=80'],
                'published_at' => '2026-05-25 09:00:00',
                'sold_at' => '2026-06-01 19:10:00',
            ],
            [
                'key' => 'student_beibit',
                'title' => 'Мини-холодильник для комнаты',
                'category' => 'electronics',
                'condition' => 'good',
                'price' => 35000,
                'pickup_location' => 'Residence A, 3 этаж',
                'status' => 'active',
                'description' => 'Компактный холодильник, работает тихо. Самовывоз из комнаты.',
                'images' => ['https://images.unsplash.com/photo-1586422338789-4c84a64d8792?auto=format&fit=crop&w=900&q=80'],
                'published_at' => '2026-06-04 14:20:00',
            ],
        ];

        foreach ($listings as $payload) {
            $listing = BuySellListing::updateOrCreate(
                [
                    'user_id' => $this->students[$payload['key']]->id,
                    'title' => $payload['title'],
                ],
                [
                    'category' => $payload['category'],
                    'condition' => $payload['condition'],
                    'price' => $payload['price'],
                    'pickup_location' => $payload['pickup_location'],
                    'contact_phone' => $this->students[$payload['key']]->phone_number,
                    'status' => $payload['status'],
                    'description' => $payload['description'],
                    'image_paths' => $payload['images'],
                    'published_at' => $payload['published_at'] ?? null,
                    'sold_at' => $payload['sold_at'] ?? null,
                ]
            );

            $this->stamp($listing, $payload['published_at'] ?? '2026-06-01 10:00:00');
        }
    }

    private function seedNews(): void
    {
        $news = [
            [
                'title' => 'Открыта запись на летнее проживание',
                'description' => 'Студенты могут подать заявку на летнее проживание через личный кабинет до 20 июня. После проверки документов менеджер подтвердит место в общежитии.',
                'translations' => [
                    'kk' => [
                        'title' => 'Жазғы тұруға өтінім қабылдау ашылды',
                        'description' => 'Студенттер 20 маусымға дейін жеке кабинет арқылы жазғы тұруға өтінім бере алады. Құжаттар тексерілгеннен кейін менеджер жатақханадағы орынды растайды.',
                    ],
                    'en' => [
                        'title' => 'Summer housing applications are open',
                        'description' => 'Students can submit a summer housing request through the personal cabinet until June 20. After document review, the manager will confirm the dormitory place.',
                    ],
                ],
                'photo' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80',
                'created_at' => '2026-06-08 09:00:00',
            ],
            [
                'title' => 'Плановая проверка комнат пройдет в пятницу',
                'description' => 'В пятницу с 15:00 до 18:00 администрация проведет плановую проверку чистоты комнат и общих зон. Просим заранее вынести мусор и освободить проходы.',
                'translations' => [
                    'kk' => [
                        'title' => 'Жұма күні бөлмелер жоспарлы тексеріледі',
                        'description' => 'Жұма күні 15:00-18:00 аралығында әкімшілік бөлмелер мен ортақ аймақтардың тазалығын тексереді. Қоқысты алдын ала шығарып, өту жолдарын босатыңыздар.',
                    ],
                    'en' => [
                        'title' => 'Scheduled room inspection on Friday',
                        'description' => 'On Friday from 15:00 to 18:00, administration will inspect room cleanliness and common areas. Please take out trash and keep passages clear in advance.',
                    ],
                ],
                'photo' => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=1200&q=80',
                'created_at' => '2026-06-06 10:30:00',
            ],
            [
                'title' => 'Обновлено расписание тренажерного зала',
                'description' => 'С 10 июня тренажерный зал работает с 07:00 до 22:00. Для посещения оформите абонемент в разделе Gym booking и отметьте check-in на входе.',
                'translations' => [
                    'kk' => [
                        'title' => 'Тренажер залының кестесі жаңартылды',
                        'description' => '10 маусымнан бастап тренажер залы 07:00-22:00 аралығында жұмыс істейді. Кіру үшін Gym booking бөлімінде абонемент алып, кіреберісте check-in жасаңыз.',
                    ],
                    'en' => [
                        'title' => 'Gym schedule updated',
                        'description' => 'Starting June 10, the gym is open from 07:00 to 22:00. Purchase a membership in Gym booking and check in at the entrance before training.',
                    ],
                ],
                'photo' => 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1200&q=80',
                'created_at' => '2026-06-04 15:45:00',
            ],
            [
                'title' => 'Campus Market: неделя учебников',
                'description' => 'На этой неделе в разделе Buy and sell действует подборка объявлений с учебниками и техникой для летней сессии. Добавляйте товары с актуальными контактами.',
                'translations' => [
                    'kk' => [
                        'title' => 'Campus Market: оқулықтар апталығы',
                        'description' => 'Осы аптада Buy and sell бөлімінде жазғы сессияға арналған оқулықтар мен техника хабарландырулары жинақталды. Байланыс деректері дұрыс көрсетілген тауарлар қосыңыз.',
                    ],
                    'en' => [
                        'title' => 'Campus Market: textbook week',
                        'description' => 'This week, Buy and sell highlights textbooks and electronics for the summer session. Add your listings with up-to-date contact details.',
                    ],
                ],
                'photo' => 'https://images.unsplash.com/photo-1491841550275-ad7854e35ca6?auto=format&fit=crop&w=1200&q=80',
                'created_at' => '2026-06-02 13:00:00',
            ],
        ];

        foreach ($news as $payload) {
            $item = News::updateOrCreate(
                ['title' => $payload['title']],
                [
                    'description' => $payload['description'],
                    'translations' => array_merge([
                        'ru' => [
                            'title' => $payload['title'],
                            'description' => $payload['description'],
                        ],
                    ], $payload['translations']),
                    'photo' => $payload['photo'],
                ]
            );

            $this->stamp($item, $payload['created_at']);
        }
    }

    private function seedSystemNotifications(): void
    {
        $notifications = [
            [
                'title' => 'Плановое отключение горячей воды',
                'message' => '12 июня с 10:00 до 14:00 в Residence A будет временно отключена горячая вода из-за технических работ.',
                'action_url' => '/news',
                'created_at' => '2026-06-08 18:00:00',
                'translations' => [
                    'kk' => [
                        'title' => 'Ыстық су уақытша өшіріледі',
                        'message' => '12 маусым күні 10:00-14:00 аралығында Residence A корпусында техникалық жұмыстарға байланысты ыстық су уақытша өшіріледі.',
                    ],
                    'en' => [
                        'title' => 'Scheduled hot water maintenance',
                        'message' => 'On June 12 from 10:00 to 14:00, hot water in Residence A will be temporarily unavailable due to maintenance.',
                    ],
                ],
            ],
            [
                'title' => 'Проверьте неоплаченные начисления',
                'message' => 'Если у вас есть начисления за проживание или спортзал, оплатите их до конца недели в разделе Финансовый кабинет.',
                'action_url' => '/finance',
                'created_at' => '2026-06-07 12:00:00',
                'translations' => [
                    'kk' => [
                        'title' => 'Төленбеген начислениелерді тексеріңіз',
                        'message' => 'Тұру немесе спортзал бойынша төлеміңіз болса, оны апта соңына дейін Қаржы кабинеті бөлімінде төлеңіз.',
                    ],
                    'en' => [
                        'title' => 'Check unpaid charges',
                        'message' => 'If you have housing or gym charges, pay them by the end of the week in the Finance section.',
                    ],
                ],
            ],
            [
                'title' => 'Правила штрафов обновлены',
                'message' => 'Обновленные правила дисциплинарных штрафов доступны в разделе Penalties. Максимальный лимит остается 10 баллов.',
                'action_url' => '/penalty',
                'created_at' => '2026-06-05 17:30:00',
                'translations' => [
                    'kk' => [
                        'title' => 'Айыппұл ережелері жаңартылды',
                        'message' => 'Тәртіптік айыппұлдардың жаңартылған ережелері Penalties бөлімінде қолжетімді. Максималды лимит 10 ұпай болып қалады.',
                    ],
                    'en' => [
                        'title' => 'Penalty rules updated',
                        'message' => 'Updated disciplinary penalty rules are available in the Penalties section. The maximum limit remains 10 points.',
                    ],
                ],
            ],
        ];

        foreach ($notifications as $payload) {
            $broadcast = SystemNotification::updateOrCreate(
                ['title' => $payload['title']],
                [
                    'message' => $payload['message'],
                    'translations' => array_merge([
                        'ru' => [
                            'title' => $payload['title'],
                            'message' => $payload['message'],
                        ],
                    ], $payload['translations']),
                    'action_url' => $payload['action_url'],
                    'created_by' => $this->users['manager']->id,
                ]
            );

            $this->stamp($broadcast, $payload['created_at']);
        }
    }

    private function seedInboxNotifications(): void
    {
        $demoUsers = collect($this->users)->values();
        $broadcasts = SystemNotification::query()
            ->whereIn('title', [
                'Плановое отключение горячей воды',
                'Проверьте неоплаченные начисления',
                'Правила штрафов обновлены',
            ])
            ->get();

        foreach ($broadcasts as $broadcast) {
            foreach ($demoUsers as $user) {
                $readAt = str_contains($user->email, 'admin@') || $broadcast->title === 'Правила штрафов обновлены'
                    ? Carbon::parse($broadcast->created_at)->addHours(4)
                    : null;

                $this->upsertNotification(
                    'broadcast:'.$broadcast->id.':'.$user->id,
                    SystemBroadcastNotification::class,
                    $user,
                    [
                        'broadcast_id' => $broadcast->id,
                        'title' => $broadcast->title,
                        'message' => $broadcast->message,
                        'title_ru' => $broadcast->title,
                        'message_ru' => $broadcast->message,
                        'translations' => $broadcast->translations,
                        'action_url' => $broadcast->action_url,
                        'sender_id' => $broadcast->created_by,
                        'sender_name' => $this->fullName($this->users['manager']),
                    ],
                    $broadcast->created_at,
                    $readAt
                );
            }
        }

        $student = $this->students['student_tomiris'];
        $studentName = $this->fullName($student);
        $studentTranslations = [
            'ru' => [
                'title' => 'Лимит штрафов достигнут',
                'message' => 'Ваши штрафные баллы достигли 10/10. Проживание закрыто по дисциплинарной причине. Обратитесь к администрации.',
            ],
            'kk' => [
                'title' => 'Айыппұл лимитіне жетті',
                'message' => 'Сіздің айыппұл ұпайларыңыз 10/10 деңгейіне жетті. Тұру тәртіптік себеппен жабылды. Әкімшілікке хабарласыңыз.',
            ],
            'en' => [
                'title' => 'Penalty limit reached',
                'message' => 'Your penalty points reached 10/10. Housing was closed for a disciplinary reason. Contact administration.',
            ],
        ];
        $staffTranslations = [
            'ru' => [
                'title' => 'Студент достиг лимита штрафов',
                'message' => "{$studentName} набрал 10/10 штрафных баллов. Нужно рассмотреть отчисление или выселение из общежития.",
            ],
            'kk' => [
                'title' => 'Студент айыппұл лимитіне жетті',
                'message' => "{$studentName} 10/10 айыппұл ұпайын жинады. Жатақханадан шығару немесе оқудан шығару мәселесін қарау керек.",
            ],
            'en' => [
                'title' => 'Student reached the penalty limit',
                'message' => "{$studentName} reached 10/10 penalty points. Review expulsion or dormitory eviction.",
            ],
        ];

        $this->upsertDisciplineNotification($student, $studentTranslations, '/penalty', null);

        foreach (['manager', 'dorm_admin', 'admin'] as $key) {
            $url = $key === 'manager' ? '/manager/penalties' : '/dorm-admin/penalties';
            $this->upsertDisciplineNotification($this->users[$key], $staffTranslations, $url, null);
        }
    }

    private function upsertPenalty(
        string $studentKey,
        string $ruleCode,
        int $points,
        string $status,
        string $description,
        string $createdAt,
        array $evidences,
    ): Penalty {
        $student = $this->students[$studentKey];
        $settlement = $this->settlements[$studentKey];
        $rule = $this->penaltyRules[$ruleCode];

        $penalty = Penalty::updateOrCreate(
            [
                'user_id' => $student->id,
                'rule_id' => $rule->id,
                'description' => $description,
            ],
            [
                'settlement_id' => $settlement->id,
                'created_by' => $this->users['dorm_admin']->id,
                'points' => $points,
                'status' => $status,
            ]
        );

        $this->stamp($penalty, $createdAt);

        foreach ($evidences as $path) {
            PenaltyEvidence::updateOrCreate(
                [
                    'penalty_id' => $penalty->id,
                    'file_path' => $path,
                ],
                []
            );
        }

        return $penalty;
    }

    private function upsertRedemption(
        Penalty $penalty,
        string $eventType,
        string $description,
        string $status,
        ?int $reviewedBy,
        string $createdAt,
    ): void {
        $redemption = PenaltyRedemption::updateOrCreate(
            [
                'penalty_id' => $penalty->id,
                'event_type' => $eventType,
            ],
            [
                'user_id' => $penalty->user_id,
                'description' => $description,
                'file_path' => 'redemptions/'.$penalty->id.'/report.pdf',
                'status' => $status,
                'reviewed_by' => $reviewedBy,
                'reviewed_at' => $reviewedBy ? Carbon::parse($createdAt)->addDay() : null,
            ]
        );

        $this->stamp($redemption, $createdAt);
    }

    private function seedPayment(Charge $charge, string $sessionId, string $paidAt): void
    {
        Payment::updateOrCreate(
            ['stripe_session_id' => $sessionId],
            [
                'charge_id' => $charge->id,
                'stripe_payment_intent_id' => 'pi_demo_'.substr(md5($sessionId), 0, 16),
                'amount' => $charge->amount,
                'status' => 'succeeded',
                'paid_at' => $paidAt,
                'raw_payload' => [
                    'source' => 'production_demo_seeder',
                    'session_id' => $sessionId,
                    'payment_status' => 'paid',
                ],
            ]
        );
    }

    /**
     * @param array{date: string, in: string, out: string, minutes: int} $visit
     */
    private function upsertGymVisit(GymMembership $membership, User $user, array $visit): void
    {
        $gymVisit = GymVisit::query()
            ->where('membership_id', $membership->id)
            ->whereDate('visit_date', $visit['date'])
            ->first();

        if (! $gymVisit) {
            $gymVisit = new GymVisit([
                'membership_id' => $membership->id,
                'visit_date' => $visit['date'],
            ]);
        }

        $gymVisit->fill([
            'user_id' => $user->id,
            'check_in_at' => Carbon::parse($visit['date'].' '.$visit['in']),
            'check_out_at' => Carbon::parse($visit['date'].' '.$visit['out']),
            'duration_minutes' => $visit['minutes'],
            'sessions_used' => 1,
            'status' => 'completed',
        ]);

        $gymVisit->save();
    }

    private function upsertDisciplineNotification(
        User $recipient,
        array $translations,
        string $actionUrl,
        ?Carbon $readAt,
    ): void {
        $ru = $translations['ru'];

        $this->upsertNotification(
            'discipline-limit:'.$recipient->id,
            DisciplineLimitReachedNotification::class,
            $recipient,
            [
                'notification_type' => 'discipline_limit_reached',
                'title' => $ru['title'],
                'message' => $ru['message'],
                'title_ru' => $ru['title'],
                'message_ru' => $ru['message'],
                'translations' => $translations,
                'action_url' => $actionUrl,
                'sender_name' => null,
                'student_id' => $this->students['student_tomiris']->id,
                'student_name' => $this->fullName($this->students['student_tomiris']),
                'active_points' => 10,
                'discipline_limit' => 10,
            ],
            '2026-04-18 09:05:00',
            $readAt
        );
    }

    private function upsertNotification(
        string $key,
        string $type,
        User $recipient,
        array $data,
        string|Carbon $createdAt,
        ?Carbon $readAt,
    ): void {
        $createdAt = $createdAt instanceof Carbon ? $createdAt : Carbon::parse($createdAt);

        DB::table('notifications')->updateOrInsert(
            ['id' => $this->deterministicUuid($key)],
            [
                'type' => $type,
                'notifiable_type' => User::class,
                'notifiable_id' => $recipient->id,
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'read_at' => $readAt,
                'created_at' => $createdAt,
                'updated_at' => now(),
            ]
        );
    }

    private function room(string $buildingAddress, int $roomNumber): Room
    {
        return Room::query()
            ->where('room_number', $roomNumber)
            ->whereHas('floor.building', fn ($query) => $query->where('address', $buildingAddress))
            ->firstOrFail();
    }

    private function stamp(Model $model, string $createdAt): void
    {
        $model->forceFill([
            'created_at' => Carbon::parse($createdAt),
            'updated_at' => now(),
        ])->saveQuietly();
    }

    private function fullName(User $user): string
    {
        return trim(implode(' ', array_filter([
            $user->lastname,
            $user->name,
            $user->middlename,
        ]))) ?: $user->email;
    }

    private function deterministicUuid(string $key): string
    {
        $hash = md5($key);

        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            substr($hash, 12, 4),
            substr($hash, 16, 4),
            substr($hash, 20, 12),
        );
    }
}
