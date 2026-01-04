<?php

namespace App\Main;
use Illuminate\Support\Facades\DB;

class SideMenu
{
    /**
     * List of side menu items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function menu()
    {

         $professionTitle = DB::table('systemflag')
                ->where('name', 'professionTitle')
                ->select('value')
                ->first();
            $professionTitle = $professionTitle ? $professionTitle->value : 'Partner';

        return [

            'dashboard' => [
                'icon' => 'home',
                'route_name' => 'dashboard',
                'params' => "",
                'title' => 'Dashboard',
            ],

            'customer-list' => [
                'icon' => 'users',
                'route_name' => 'customers',
                'params' => "",
                'title' => 'Customers',
            ],
            'astrologer-list' => [
                'icon' => 'target',
                'title' =>  $professionTitle,
                'sub_menu' => [
                    'block'.$professionTitle => [
                        'icon' => 'x',
                        'route_name' => 'blockAstrologer',
                        'params' => "",
                        'title' => 'Block'.$professionTitle,
                    ],
                    'manage'.$professionTitle => [
                        'icon' => 'folder',
                        'route_name' =>'astrologers' ,
                        'params' => "",
                        'title' => 'Manage'.$professionTitle,
                    ],
                    'pendingrequests' => [
                        'icon' => 'circle',
                        'route_name' => 'pending-requests',
                        'params' => "",
                        'title' => 'Pending Requests',
                    ],
                    'reviews' => [
                        'icon' => 'star',
                        'route_name' => 'astrologerReview',
                        'params' => "",
                        'title' => 'Reviews',
                    ],
                    'gifts' => [
                        'icon' => 'gift',
                        'route_name' => 'gifts',
                        'params' => "",
                        'title' => 'Gifts',
                    ],
                    'skills' => [
                        'icon' => 'pocket',
                        'route_name' => 'skills',
                        'params' => "",
                        'title' => 'Skills',
                    ],
                    'categories' => [
                        'icon' => 'asterisk',
                        'route_name' => 'astrologerCategories',
                        'params' => "",
                        'title' => 'Categories',
                    ],
                    'commissionn' => [
                        'icon' => 'indian-rupee',
                        'route_name' => 'commissions',
                        'params' => "",
                        'title' => 'Commissionn rate for calls/ chats',
                    ],
                    'profileboost' => [
                        'icon' => 'rocket',
                        'route_name' => 'profile-list',
                        'params' => "",
                        'title' => 'Profile Boost',
                    ],
                ],
            ],
            'astroMall' => [
                'icon' => 'layers',
                'title' => 'Astroshop',
                'sub_menu' => [
                    'productCategory' => [
                        'icon' => '',
                        'route_name' => 'productCategories',
                        'params' => "",
                        'title' => 'Product Categories',
                    ],
                    'product' => [
                        'icon' => '',
                        'route_name' => 'products',
                        'params' => "",
                        'title' => 'Products',
                    ],
                    'order' => [
                        'icon' => '',
                        'route_name' => 'orders',
                        'params' => "",
                        'title' => 'Orders',
                    ],
                ],
            ],
            'daily-horoscope' => [
                'icon' => 'book-open',
                'title' => 'Horoscope',
                'sub_menu' => [
                    'dailyHoroScope' => [
                        'icon' => '',
                        'route_name' => 'dailyHoroscope',
                        'params' => "",
                        'title' => 'Daily HoroScope',
                    ],
                    // 'dailyHoroScopeInsight' => [
                    //     'icon' => '',
                    //     'route_name' => 'dailyHoroscopeInsight',
                    //     'params' => "",
                    //     'title' => 'Horoscope Insights',
                    // ],
                    'horoscope' => [
                        'icon' => '',
                        'route_name' => 'horoscope',
                        'params' => "",
                        'title' => 'Weekly & Yearly Horoscope',
                    ],
                    'horoscopeFeedback' => [
                        'icon' => '',
                        'route_name' => 'horoscopeFeedback',
                        'params' => "",
                        'title' => 'Horoscope Feedback',
                    ],
                ]],

  'puja' => [
                'icon' => 'aperture',
                'title' => 'Puja',
                'sub_menu' => [
                    'categories' => [
                        'icon' => 'asterisk',
                        'route_name' => 'puja-categories-list',
                        'params' => "",
                        'title' => 'Categories',
                    ],
                    'subcategories' => [
                        'icon' => 'asterisk',
                        'route_name' => 'puja-subcategories-list',
                        'params' => "",
                        'title' => 'Sub Categories',
                    ],
                    'pujapackage' => [
                        'icon' => 'package',
                        'route_name' => 'package-list',
                        'params' => "",
                        'title' => 'Puja Package',
                    ],
                    'puja' => [
                        'icon' => 'box',
                        'route_name' => 'puja-list',
                        'params' => "",
                        'title' => 'Puja',
                    ],
                    'pujafaqs' => [
                        'icon' => 'align-justify',
                        'route_name' => 'puja-faq-list',
                        'params' => "",
                        'title' => 'Puja Faq',
                    ],
                    'pujaorder' => [
                        'icon' => 'shopping-cart',
                        'route_name' => 'puja-order-list',
                        'params' => "",
                        'title' => 'Puja Order',
                    ],
                ],
            ],
            'course' => [
                'icon' => 'book',
                'title' => 'Course',
                'sub_menu' => [
                    'categories' => [
                        'icon' => 'asterisk',
                        'route_name' => 'course-categories-list',
                        'params' => "",
                        'title' => 'Categories',
                    ],
                    'course' => [
                        'icon' => 'package',
                        'route_name' => 'CourseList-list',
                        'params' => "",
                        'title' => 'Course',
                    ],
                    'chapters' => [
                        'icon' => 'package',
                        'route_name' => 'course-chapter-list',
                        'params' => "",
                        'title' => 'Chapters',
                    ],
                    'courseorders' => [
                        'icon' => 'shopping-cart',
                        'route_name' => 'courseOrderList',
                        'params' => "",
                        'title' => 'Course Orders',
                    ],

                ],
            ],



            'blog-list' => [
                'icon' => 'edit',
                'route_name' => 'blogs',
                'params' => "",
                'title' => 'Blogs',
            ],
            'news' => [
                'icon' => 'airplay',
                'route_name' => 'astroguruNews',
                'params' => "",
                'title' => 'News',
            ],

            'adsVideo' => [
                'icon' => 'video',
                'route_name' => 'adsVideos',
                'params' => "",
                'title' => 'Videos',
            ],
             'banner-list' => [
                'icon' => 'image',
                'route_name' => 'banners',
                'params' => "",
                'title' => 'Banner Management',
                ],
            'stories' => [
                'icon' => 'play-circle',
                'route_name' => 'story-list',
                'params' => "",
                'title' => 'Stories',
                ],
             'notification-list' => [
                    'icon' => 'bell-ring',
                    'route_name' => 'notifications',
                    'params' => "",
                    'title' => 'Notifications',
                ],

            'earning' => [
                'icon' => 'dollar-sign',
                'title' => 'Support Management',
                'sub_menu' => [
                   'ticket' => [
                    'icon' => 'twitch',
                    'route_name' => 'tickets',
                    'params' => "",
                    'title' => 'Support Tickets',
                ],
                    'faqs' => [
                        'icon' => 'list',
                        'route_name' => 'helpSupport',
                        'params' => "",
                        'title' => 'FAQs',
                    ],
                ],
            ],

            'earning' => [
                'icon' => 'dollar-sign',
                'title' => 'Earnings',
                'sub_menu' => [
                    'withdrawl' => [
                        'icon' => 'dollar-sign',
                        'route_name' => 'withdrawalRequests',
                        'params' => "",
                        'title' => 'Withdrawal Requests',
                    ],
                    'withdrawal-methods' => [
                        'icon' => 'activity',
                        'route_name' => 'withdrawalMethods',
                        'params' => "",
                        'title' => 'Withdrawal Methods',
                    ],
                    'withdrawal-methods' => [
                        'icon' => 'history',
                        'route_name' => 'walletHistory',
                        'params' => "",
                        'title' => 'Wallet History',
                    ],
                ],
            ],
            'report' => [
                'icon' => 'file',
                'title' => 'Reports',
                'sub_menu' => [
                    'callHistory' => [
                        'icon' => 'phone',
                        'route_name' => 'callHistory',
                        'params' => "",
                        'title' => 'Call History',
                    ],
                    'chatHistory' => [
                        'icon' => 'chat',
                        'route_name' => 'chatHistory',
                        'params' => "",
                        'title' => 'Chat History',
                    ],
                    'partnerWiseEarning' => [
                        'icon' => 'doller',
                        'route_name' => 'partnerWiseEarning',
                        'params' => "",
                        'title' => 'PartnerWise Earning',
                    ],
                    'orderRequest' => [
                        'icon' => 'doller',
                        'route_name' => 'orderrequest',
                        'params' => "",
                        'title' => 'Order Request',
                    ],
                    'reportRequest' => [
                        'icon' => 'doller',
                        'route_name' => 'reportrequest',
                        'params' => "",
                        'title' => 'Report Request',
                    ],
                    'kundali' => [
                        'icon' => 'files',
                        'route_name' => 'kundaliearning',
                        'params' => "",
                        'title' => 'Kundali Earning',
                    ],
                    'exotel-report-list' => [
                        'icon' => 'phone',
                        'route_name' => 'exotel-report-list',
                        'params' => "",
                        'title' => 'Exotel Call History',
                    ],
                ],

            ],

            'master-page' => [
                'icon' => 'book-open',
                'title' => 'Master Settings',
                'sub_menu' => [
                    'customer-profile' => [
                        'icon' => '',
                        'route_name' => 'customerProfile',
                        'params' => "",
                        'title' => 'Customer Profile',
                    ],
                    'horor-scope-sign-list' => [
                        'icon' => '',
                        'route_name' => 'horoscopeSigns',
                        'params' => "",
                        'title' => 'HoroScope Signs',
                    ],
                    'reports' => [
                        'icon' => 'folder',
                        'route_name' => 'reportTypes',
                        'params' => "",
                        'title' => 'Report Type',
                    ],
                    'recharge-amount' => [
                        'icon' => '',
                        'route_name' => 'rechargeAmount',
                        'params' => "",
                        'title' => 'Recharge Amount',
                    ],
                    'kundali-prices' => [
                        'icon' => '',
                        'route_name' => 'kundali-prices',
                        'params' => "",
                        'title' => 'Kundali Price',
                    ],
                     'referral-settings' => [
                        'icon' => '',
                        'route_name' => 'referral-settings',
                        'params' => "",
                        'title' => 'Referral Settings',
                    ],
                ],
            ],
             'teammanagement' => [
                'icon' => 'user',
                'title' => 'Team Management',
                'sub_menu' => [
                    'teamRole' => [
                        'icon' => 'equal',
                        'route_name' => 'teamRole',
                        'params' => "",
                        'title' => 'Team Role',
                    ],
                    'team-list' => [
                        'icon' => 'users',
                        'route_name' => 'team-list',
                        'params' => "",
                        'title' => 'Team List',
                    ],
                ],
            ],
            'systemFlag' => [
                'icon' => 'settings',
                'route_name' => 'setting',
                'params' => "",
                'title' => 'General Settings',
            ],
            'appFeedback' => [
                'icon' => 'message-square',
                'route_name' => 'feedback',
                'params' => "",
                'title' => 'Feedback',
            ],
            'pages' => [
                'icon' => 'book-open',
                'route_name' => 'pages',
                'params' => "",
                'title' => 'Page Management',
            ],
            'contactlist' => [
                'icon' => 'contact',
                'route_name' => 'contactlist',
                'params' => "",
                'title' => 'Contact Form',
            ],
        ];
    }
}
