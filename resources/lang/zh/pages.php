<?php

return [
    'tours' => [
        'page' => [
            'title' => '拉布安巴焦旅行套餐 | TriptoKomodo',
            'meta' => '探索拉布安巴焦旅行套餐：高品质船只、灵活行程、本地向导、透明价格。支持多语言快速预订。',
            'keywords' => '拉布安巴焦 旅行套餐, 科莫多 旅行, 科莫多 国家公园, 私人团 拉布安巴焦, 科莫多 liveaboard',
        ],
        'hero' => [
            'tag' => '旅行套餐',
            'headline' => '适合各种出行风格的拉布安巴焦套餐',
            'sub' => '选择天数、船型与最适合你的旅行体验。',
        ],
        'filters' => [
            'chip_category' => '船型：:value',
            'chip_duration' => '时长：:value',
            'chip_destination' => '目的地：:value',
            'reset' => '重置',
            'total' => '套餐总数：:count',
            'duration_day' => '天',
            'duration_night' => '晚',
            'card_summary_fallback' => '高品质拉布安巴焦旅行套餐。',
            'card_cta' => '查看详情 →',
            'card_unavailable' => '暂无详情',
        ],
        'cards' => [
            'from' => '起',
            'per_person' => '每人',
            'see_detail' => '查看详情',
        ],
        'detail' => [
            'meta_description_fallback' => '高品质拉布安巴焦旅行套餐。',
            'price_suffix' => '/ 人',
            'currency_idr' => 'Rp',

            'cta_overview' => '查看详情',
            'cta_consult' => '行程咨询',

            'badge_max' => '最多 :count',

            'stats' => [
                'price_from' => '起价',
                'operator' => '运营方',
                'availability' => '可用日期',
            ],

            'availability_not_set' => '尚未设置',
            'availability_count' => ':count 个日期可用',

            'booking' => [
                'title' => '快速预订',
                'note' => '价格会根据所选货币自动调整。',
                'min' => '最少',
                'max' => '最多',
                'pax' => '人',
                'status' => '状态',
                'cta_consult_book' => '咨询并预订',
                'cta_check_availability' => '查看可用日期',
            ],

            'status' => [
                'published' => '已发布',
                'draft' => '草稿',
                'archived' => '已归档',
                'unknown' => '未知',
            ],

            'sections' => [
                'description' => '行程介绍',
                'itinerary' => '行程安排',
                'itinerary_fallback' => '完整行程将由我们的管家提供。',
                'included' => '包含',
                'excluded' => '不包含',
                'included_fallback' => '船宿、餐食、船员与拍摄记录。',
                'excluded_fallback' => '机票、个人保险与个人消费。',
                'transportation' => '交通',
                'destinations' => '目的地',
                'destinations_fallback' => '旅行中将前往的精彩目的地。',
                'view_on_maps' => '在地图中查看',
            ],

            'availability' => [
                'title' => '可用日期',
                'empty' => '该套餐尚未设置可用日期日历。请联系管家确认日期。',
                'calendar_days' => ['日', '一', '二', '三', '四', '五', '六'],
                'slot_count' => ':count 个名额',
                'legend_available' => '可用',
                'legend_closed' => '满员/关闭',
            ],

            'actions' => [
                'ask_schedule' => '询问日期并预订',
                'view_other_packages' => '查看其他套餐',
            ],

            'faq' => [
                'title' => '常见问题',
                'empty' => '常见问题即将上线。',
            ],

            'reviews' => [
                'title' => '评价',
                'summary' => '评分 :rating/5 • :count 条评价',
                'reviewer_fallback' => '旅行者',
                'rating_label' => '评分：',
                'empty' => '暂无评价。',
            ],

            'summary' => [
                'title' => '摘要',
                'duration' => '时长',
                'capacity' => '人数',
                'category' => '分类',
                'operator' => '运营方',
                'cta_consult' => '立即咨询',
                'cta_availability' => '查看可用日期',
            ],
        ],
    ],

    'rental' => [
        'page' => [
            'title' => '拉布安巴焦包车 | TriptoKomodo',
            'meta' => '拉布安巴焦包车：专业司机、舒适车辆、灵活行程（弗洛勒斯及周边）。可通过 WhatsApp 快速咨询。',
            'keywords' => '拉布安巴焦 包车, 弗洛勒斯 用车, 拉布安巴焦 司机, 租车 拉布安巴焦',
        ],
        'hero' => [
            'tag' => '包车服务',
            'title' => '拉布安巴焦包车',
            'desc' => '本页面可用于展示包车套餐（车型、价格、时长与司机服务）。目前请联系团队获取最快推荐。',
        ],
        'cars' => [
            'title' => '可选车型',
            'subtitle' => '根据路线和人数选择合适的车辆。',
            'empty' => '当前暂无可展示的租车车辆。',
            'from' => '起',
            'per_day' => '每天',
            'see_detail' => '查看详情',
        ],
        'cta' => [
            'title' => '需要快速推荐？',
            'desc' => '点击咨询，获取适合你弗洛勒斯路线的车辆选项。',
            'button' => '咨询',
        ],
    ],

    'blog' => [
        'page' => [
            'title' => 'Komodo Insider | TriptoKomodo',
            'meta' => 'Komodo Insider：拉布安巴焦、科莫多与弗洛勒斯旅行的文章、行程与实用攻略。',
            'keywords' => 'Komodo Insider, 拉布安巴焦 攻略, 科莫多 行程, 科莫多 旅行指南',
        ],
        'hero' => [
            'tag' => 'Komodo Insider',
            'title' => '博客与攻略',
            'desc' => '这里将发布文章（旅行建议、行程、最佳景点）。目前仍在搭建中。',
        ],
        'card' => [
            'tag' => 'Komodo Insider',
            'title' => 'Komodo Insider 文章',
            'desc' => '管理员发布文章后，内容会显示在这里。',
        ],
    ],

    'contact' => [
        'page' => [
            'keywords' => '联系 TriptoKomodo, 拉布安巴焦 预订, 科莫多 咨询, WhatsApp 拉布安巴焦',
        ],
    ],
];
