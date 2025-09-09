// Enhanced Quiz Data for WordPress Implementation
// Comprehensive Fidelity Quiz Questions

const femaleQuestions = [
    // 1-10: Telefon və gizlilik
    {
        id: 'f-phone-1',
        text: 'Telefonu masaya qoyanda ekranı həmişə aşağı olur?',
        topic: 'phone',
        answers: ['Bəli', 'Bəzən', 'Xeyr']
    },
    {
        id: 'f-phone-2', 
        text: 'Mesaj bildirişlərini tez-tez söndürür?',
        topic: 'phone',
        answers: ['Həmişə', 'Ara-sıra', 'Yox']
    },
    {
        id: 'f-phone-3',
        text: 'Telefon parolunu səninlə paylaşmaqdan imtina edir?',
        topic: 'phone',
        answers: ['Tam imtina edir', 'Bəzən narahat olur', 'Rahat paylaşır']
    },
    {
        id: 'f-phone-4',
        text: 'Zənglərinə cavab verməkdə gecikir?',
        topic: 'phone',
        answers: ['Çox vaxt', 'Nadir hallarda', 'Heç vaxt']
    },
    {
        id: 'f-phone-5',
        text: 'Sosial şəbəkələrdə səndən gizli hesabları var?',
        topic: 'phone',
        answers: ['Bəli', 'Şübhələnirəm', 'Yox']
    },
    {
        id: 'f-phone-6',
        text: 'Qalereyasında sənə göstərmək istəmədiyi şəkillər olur?',
        topic: 'phone',
        answers: ['Bəli, tez-tez', 'Bəzən', 'Heç vaxt']
    },
    {
        id: 'f-phone-7',
        text: 'Telefonu ilə vaxt keçirməsi artıb?',
        topic: 'phone',
        answers: ['Həmişə əlindədir', 'Normal qədər', 'Az istifadə edir']
    },
    {
        id: 'f-phone-8',
        text: 'Telefonu sənin yanında açmamağa çalışır?',
        topic: 'phone',
        answers: ['Bəli', 'Bəzən', 'Xeyr']
    },
    {
        id: 'f-phone-9',
        text: 'Mesajları tez-tez silir?',
        topic: 'phone',
        answers: ['Həmişə', 'Ara-sıra', 'Heç vaxt']
    },
    {
        id: 'f-phone-10',
        text: 'Gecələr telefondan kənara çəkilir?',
        topic: 'phone',
        answers: ['Bəli', 'Bəzən', 'Xeyr']
    },
    
    // 11-20: Gündəlik davranışlar
    {
        id: 'f-behavior-1',
        text: 'İşdən sonra evə gec gəlir?',
        topic: 'behavior',
        answers: ['Tez-tez', 'Bəzən', 'Heç vaxt']
    },
    {
        id: 'f-behavior-2',
        text: '"İclasdayam" bəhanəsi çoxalır?',
        topic: 'behavior',
        answers: ['Həmişə deyir', 'Bəzən deyir', 'Nadirdir']
    },
    {
        id: 'f-behavior-3',
        text: 'Geyim tərzini dəyişib?',
        topic: 'behavior',
        answers: ['Qəti şəkildə dəyişib', 'Kiçik dəyişikliklər var', 'Heç nə dəyişməyib']
    },
    {
        id: 'f-behavior-4',
        text: 'Yeni ətir və ya saç düzümü istifadə edir?',
        topic: 'behavior',
        answers: ['Bəli, tez-tez', 'Bəzən', 'Yox']
    },
    {
        id: 'f-behavior-5',
        text: 'Dostlarla görüşləri çoxalıb?',
        topic: 'behavior',
        answers: ['Həmişə gedir', 'Bəzən', 'Yox']
    },
    {
        id: 'f-behavior-6',
        text: 'Maşında qəribə əşyalar tapmısan?',
        topic: 'behavior',
        answers: ['Bəli, bir neçə dəfə', 'Bir dəfə olub', 'Heç vaxt']
    },
    {
        id: 'f-behavior-7',
        text: 'Evə gələndə səbəbsiz əsəbi olur?',
        topic: 'behavior',
        answers: ['Tez-tez', 'Bəzən', 'Yox']
    },
    {
        id: 'f-behavior-8',
        text: 'Özünü sənə qarşı həddən artıq mehriban aparır?',
        topic: 'behavior',
        answers: ['Bəli, birdən-birə', 'Bəzən', 'Yox']
    },
    {
        id: 'f-behavior-9',
        text: 'Yeni maraq dairələri ortaya çıxıb?',
        topic: 'behavior',
        answers: ['Qəti şəkildə', 'Qismən', 'Heç vaxt']
    },
    {
        id: 'f-behavior-10',
        text: 'Evdə az vaxt keçirir?',
        topic: 'behavior',
        answers: ['Çox az', 'Normal qədər', 'Çox vaxt evdədir']
    },
    
    // 21-30: Münasibət və yaxınlıq
    {
        id: 'f-intimacy-1',
        text: 'Fiziki yaxınlıqdan yayınır?',
        topic: 'intimacy',
        answers: ['Tez-tez', 'Bəzən', 'Yox']
    },
    {
        id: 'f-intimacy-2',
        text: 'Səninlə danışanda diqqəti başqa yerdə olur?',
        topic: 'intimacy',
        answers: ['Həmişə', 'Ara-sıra', 'Yox']
    },
    {
        id: 'f-intimacy-3',
        text: 'Göz kontaktından qaçır?',
        topic: 'intimacy',
        answers: ['Tez-tez', 'Bəzən', 'Heç vaxt']
    },
    {
        id: 'f-intimacy-4',
        text: 'Komplimentləri azalıb?',
        topic: 'intimacy',
        answers: ['Tamamilə yox olub', 'Bəzən deyir', 'Həmişə deyir']
    },
    {
        id: 'f-intimacy-5',
        text: 'Səninlə gələcək planlardan danışmır?',
        topic: 'intimacy',
        answers: ['Tamamilə', 'Bəzən', 'Normal danışır']
    },
    {
        id: 'f-intimacy-6',
        text: 'Münasibət problemləri üçün səni günahlandırır?',
        topic: 'intimacy',
        answers: ['Tez-tez', 'Ara-sıra', 'Yox']
    },
    {
        id: 'f-intimacy-7',
        text: 'Sevgisini daha az ifadə edir?',
        topic: 'intimacy',
        answers: ['Qəti şəkildə', 'Bəzən', 'Həmişə ifadə edir']
    },
    {
        id: 'f-intimacy-8',
        text: 'Evdə vaxt keçirəndə uzaq davranır?',
        topic: 'intimacy',
        answers: ['Çox vaxt', 'Bəzən', 'Yox']
    },
    {
        id: 'f-intimacy-9',
        text: 'Fiziki toxunuşlardan qaçır?',
        topic: 'intimacy',
        answers: ['Tez-tez', 'Ara-sıra', 'Heç vaxt']
    },
    {
        id: 'f-intimacy-10',
        text: '"Mənim işim çoxdur" bəhanəsi çoxalır?',
        topic: 'intimacy',
        answers: ['Həmişə', 'Bəzən', 'Yox']
    },
    
    // 31-40: Sosial dairə və ünsiyyət
    {
        id: 'f-social-1',
        text: 'Yeni "həmkar" və ya "dost" haqqında çox danışır, amma tanış etməz?',
        topic: 'social',
        answers: ['Bəli', 'Şübhələnirəm', 'Xeyr']
    },
    {
        id: 'f-social-2',
        text: 'Sosial tədbirlərə səni aparmır?',
        topic: 'social',
        answers: ['Çox vaxt', 'Bəzən', 'Həmişə aparır']
    },
    {
        id: 'f-social-3',
        text: 'Ailəndən uzaqlaşmağa başlayıb?',
        topic: 'social',
        answers: ['Qəti şəkildə', 'Bir az', 'Yox']
    },
    {
        id: 'f-social-4',
        text: 'Sənin dostlarına qarşı soyuq davranır?',
        topic: 'social',
        answers: ['Tez-tez', 'Ara-sıra', 'Heç vaxt']
    },
    {
        id: 'f-social-5',
        text: 'Dost çevrəsi dəyişib?',
        topic: 'social',
        answers: ['Tamamilə', 'Qismən', 'Yox']
    },
    {
        id: 'f-social-6',
        text: 'Telefonunda yeni qadın adları görmüsən?',
        topic: 'social',
        answers: ['Bəli', 'Bir dəfə olub', 'Yox']
    },
    {
        id: 'f-social-7',
        text: 'Sosial şəbəkələrdə sənin şəkillərini paylaşmır?',
        topic: 'social',
        answers: ['Heç vaxt paylaşmır', 'Bəzən paylaşır', 'Normal paylaşır']
    },
    {
        id: 'f-social-8',
        text: 'İnstagram/Facebook aktivliyi artıb?',
        topic: 'social',
        answers: ['Çox', 'Normal qədər', 'Azalıb']
    },
    {
        id: 'f-social-9',
        text: '"Sadəcə dostuq" dediyi biri ilə çox ünsiyyət saxlayır?',
        topic: 'social',
        answers: ['Həmişə', 'Bəzən', 'Yox']
    },
    {
        id: 'f-social-10',
        text: 'Evdə zəngləri sənin yanında qəbul etmir?',
        topic: 'social',
        answers: ['Tez-tez', 'Ara-sıra', 'Heç vaxt']
    },
    
    // 41-50: Əlavə siqnallar  
    {
        id: 'f-additional-1',
        text: 'Pul xərclərində gizlilik artıb?',
        topic: 'additional',
        answers: ['Bəli', 'Bəzən', 'Xeyr']
    },
    {
        id: 'f-additional-2',
        text: 'Qeyri-adi saatlarda mesajlar gəlir?',
        topic: 'additional',
        answers: ['Çox tez-tez', 'Bəzən', 'Heç vaxt']
    },
    {
        id: 'f-additional-3',
        text: 'Yeni parollar qoyub və bölüşmür?',
        topic: 'additional',
        answers: ['Həmişə', 'Ara-sıra', 'Heç vaxt']
    },
    {
        id: 'f-additional-4',
        text: 'Yeni hobbisi var, amma sənlə paylaşmır?',
        topic: 'additional',
        answers: ['Tamamilə gizlidir', 'Bəzən danışır', 'Həmişə paylaşır']
    },
    {
        id: 'f-additional-5',
        text: 'Söhbətləriniz daha qısa və səthi olub?',
        topic: 'additional',
        answers: ['Qəti şəkildə', 'Bir az', 'Yox']
    },
    {
        id: 'f-additional-6',
        text: 'Özünü müdafiə etmək üçün səni "çox qısqanc" adlandırır?',
        topic: 'additional',
        answers: ['Tez-tez', 'Bəzən', 'Yox']
    },
    {
        id: 'f-additional-7',
        text: 'Şübhəli "işgüzar səfərlər" başlayıb?',
        topic: 'additional',
        answers: ['Çox vaxt', 'Nadir hallarda', 'Heç vaxt']
    },
    {
        id: 'f-additional-8',
        text: 'Gecələr telefonla pıçıldaşır?',
        topic: 'additional',
        answers: ['Tez-tez', 'Ara-sıra', 'Yox']
    },
    {
        id: 'f-additional-9',
        text: 'Evdə sənə qarşı səbrsiz davranır?',
        topic: 'additional',
        answers: ['Həmişə', 'Bəzən', 'Yox']
    },
    {
        id: 'f-additional-10',
        text: '"Mən belə adam deyiləm" ifadəsini çox vurğulayır?',
        topic: 'additional',
        answers: ['Tez-tez', 'Bəzən', 'Heç vaxt']
    }
];

const maleQuestions = [
    // 1-10: Telefon və gizlilik
    {
        id: 'm-phone-1',
        text: 'Telefonu masaya qoyanda ekranı həmişə aşağı olur?',
        topic: 'phone',
        answers: ['Bəli', 'Bəzən', 'Xeyr']
    },
    {
        id: 'm-phone-2',
        text: 'Mesaj bildirişlərini tez-tez söndürür?',
        topic: 'phone',
        answers: ['Həmişə', 'Ara-sıra', 'Yox']
    },
    {
        id: 'm-phone-3',
        text: 'Telefon parolunu səninlə paylaşmaqdan imtina edir?',
        topic: 'phone',
        answers: ['Tam imtina edir', 'Bəzən narahat olur', 'Rahat paylaşır']
    },
    {
        id: 'm-phone-4',
        text: 'Zənglərinə cavab verməkdə gecikir?',
        topic: 'phone',
        answers: ['Çox vaxt', 'Nadir hallarda', 'Heç vaxt']
    },
    {
        id: 'm-phone-5',
        text: 'Sosial şəbəkələrdə səndən gizli hesabları var?',
        topic: 'phone',
        answers: ['Bəli', 'Şübhələnirəm', 'Yox']
    },
    {
        id: 'm-phone-6',
        text: 'Qalereyasında sənə göstərmək istəmədiyi şəkillər olur?',
        topic: 'phone',
        answers: ['Bəli, tez-tez', 'Bəzən', 'Heç vaxt']
    },
    {
        id: 'm-phone-7',
        text: 'Telefonu ilə vaxt keçirməsi artıb?',
        topic: 'phone',
        answers: ['Həmişə əlindədir', 'Normal qədər', 'Az istifadə edir']
    },
    {
        id: 'm-phone-8',
        text: 'Telefonu sənin yanında açmamağa çalışır?',
        topic: 'phone',
        answers: ['Bəli', 'Bəzən', 'Xeyr']
    },
    {
        id: 'm-phone-9',
        text: 'Mesajları tez-tez silir?',
        topic: 'phone',
        answers: ['Həmişə', 'Ara-sıra', 'Heç vaxt']
    },
    {
        id: 'm-phone-10',
        text: 'Gecələr telefondan kənara çəkilir?',
        topic: 'phone',
        answers: ['Bəli', 'Bəzən', 'Xeyr']
    },
    
    // 11-20: Gündəlik davranışlar
    {
        id: 'm-behavior-1',
        text: 'İşdən sonra evə gec gəlir?',
        topic: 'behavior',
        answers: ['Tez-tez', 'Bəzən', 'Heç vaxt']
    },
    {
        id: 'm-behavior-2',
        text: '"İclasdayam" bəhanəsi çoxalır?',
        topic: 'behavior',
        answers: ['Həmişə deyir', 'Bəzən deyir', 'Nadirdir']
    },
    {
        id: 'm-behavior-3',
        text: 'Geyim tərzini dəyişib?',
        topic: 'behavior',
        answers: ['Qəti şəkildə dəyişib', 'Kiçik dəyişikliklər var', 'Heç nə dəyişməyib']
    },
    {
        id: 'm-behavior-4',
        text: 'Yeni ətir və ya saç düzümü istifadə edir?',
        topic: 'behavior',
        answers: ['Bəli, tez-tez', 'Bəzən', 'Yox']
    },
    {
        id: 'm-behavior-5',
        text: 'Dostlarla görüşləri çoxalıb?',
        topic: 'behavior',
        answers: ['Həmişə gedir', 'Bəzən', 'Yox']
    },
    {
        id: 'm-behavior-6',
        text: 'Maşında qəribə əşyalar tapmısan?',
        topic: 'behavior',
        answers: ['Bəli, bir neçə dəfə', 'Bir dəfə olub', 'Heç vaxt']
    },
    {
        id: 'm-behavior-7',
        text: 'Evə gələndə səbəbsiz əsəbi olur?',
        topic: 'behavior',
        answers: ['Tez-tez', 'Bəzən', 'Yox']
    },
    {
        id: 'm-behavior-8',
        text: 'Özünü sənə qarşı həddən artıq mehriban aparır?',
        topic: 'behavior',
        answers: ['Bəli, birdən-birə', 'Bəzən', 'Yox']
    },
    {
        id: 'm-behavior-9',
        text: 'Yeni maraq dairələri ortaya çıxıb?',
        topic: 'behavior',
        answers: ['Qəti şəkildə', 'Qismən', 'Heç vaxt']
    },
    {
        id: 'm-behavior-10',
        text: 'Evdə az vaxt keçirir?',
        topic: 'behavior',
        answers: ['Çox az', 'Normal qədər', 'Çox vaxt evdədir']
    },
    
    // 21-30: Münasibət və yaxınlıq
    {
        id: 'm-intimacy-1',
        text: 'Fiziki yaxınlıqdan yayınır?',
        topic: 'intimacy',
        answers: ['Tez-tez', 'Bəzən', 'Yox']
    },
    {
        id: 'm-intimacy-2',
        text: 'Səninlə danışanda diqqəti başqa yerdə olur?',
        topic: 'intimacy',
        answers: ['Həmişə', 'Ara-sıra', 'Yox']
    },
    {
        id: 'm-intimacy-3',
        text: 'Göz kontaktından qaçır?',
        topic: 'intimacy',
        answers: ['Tez-tez', 'Bəzən', 'Heç vaxt']
    },
    {
        id: 'm-intimacy-4',
        text: 'Komplimentləri azalıb?',
        topic: 'intimacy',
        answers: ['Tamamilə yox olub', 'Bəzən deyir', 'Həmişə deyir']
    },
    {
        id: 'm-intimacy-5',
        text: 'Səninlə gələcək planlardan danışmır?',
        topic: 'intimacy',
        answers: ['Tamamilə', 'Bəzən', 'Normal danışır']
    },
    {
        id: 'm-intimacy-6',
        text: 'Münasibət problemləri üçün səni günahlandırır?',
        topic: 'intimacy',
        answers: ['Tez-tez', 'Ara-sıra', 'Yox']
    },
    {
        id: 'm-intimacy-7',
        text: 'Sevgisini daha az ifadə edir?',
        topic: 'intimacy',
        answers: ['Qəti şəkildə', 'Bəzən', 'Həmişə ifadə edir']
    },
    {
        id: 'm-intimacy-8',
        text: 'Evdə vaxt keçirəndə uzaq davranır?',
        topic: 'intimacy',
        answers: ['Çox vaxt', 'Bəzən', 'Yox']
    },
    {
        id: 'm-intimacy-9',
        text: 'Fiziki toxunuşlardan qaçır?',
        topic: 'intimacy',
        answers: ['Tez-tez', 'Ara-sıra', 'Heç vaxt']
    },
    {
        id: 'm-intimacy-10',
        text: '"Mənim işim çoxdur" bəhanəsi çoxalır?',
        topic: 'intimacy',
        answers: ['Həmişə', 'Bəzən', 'Yox']
    },
    
    // 31-40: Sosial dairə və ünsiyyət
    {
        id: 'm-social-1',
        text: 'Yeni "həmkar" və ya "dost" haqqında çox danışır, amma tanış etməz?',
        topic: 'social',
        answers: ['Bəli', 'Şübhələnirəm', 'Xeyr']
    },
    {
        id: 'm-social-2',
        text: 'Sosial tədbirlərə səni aparmır?',
        topic: 'social',
        answers: ['Çox vaxt', 'Bəzən', 'Həmişə aparır']
    },
    {
        id: 'm-social-3',
        text: 'Ailəndən uzaqlaşmağa başlayıb?',
        topic: 'social',
        answers: ['Qəti şəkildə', 'Bir az', 'Yox']
    },
    {
        id: 'm-social-4',
        text: 'Sənin dostlarına qarşı soyuq davranır?',
        topic: 'social',
        answers: ['Tez-tez', 'Ara-sıra', 'Heç vaxt']
    },
    {
        id: 'm-social-5',
        text: 'Dost çevrəsi dəyişib?',
        topic: 'social',
        answers: ['Tamamilə', 'Qismən', 'Yox']
    },
    {
        id: 'm-social-6',
        text: 'Telefonunda yeni qadın adları görmüsən?',
        topic: 'social',
        answers: ['Bəli', 'Bir dəfə olub', 'Yox']
    },
    {
        id: 'm-social-7',
        text: 'Sosial şəbəkələrdə sənin şəkillərini paylaşmır?',
        topic: 'social',
        answers: ['Heç vaxt paylaşmır', 'Bəzən paylaşır', 'Normal paylaşır']
    },
    {
        id: 'm-social-8',
        text: 'İnstagram/Facebook aktivliyi artıb?',
        topic: 'social',
        answers: ['Çox', 'Normal qədər', 'Azalıb']
    },
    {
        id: 'm-social-9',
        text: '"Sadəcə dostuq" dediyi biri ilə çox ünsiyyət saxlayır?',
        topic: 'social',
        answers: ['Həmişə', 'Bəzən', 'Yox']
    },
    {
        id: 'm-social-10',
        text: 'Evdə zəngləri sənin yanında qəbul etmir?',
        topic: 'social',
        answers: ['Tez-tez', 'Ara-sıra', 'Heç vaxt']
    },
    
    // 41-50: Əlavə siqnallar
    {
        id: 'm-additional-1',
        text: 'Pul xərclərində gizlilik artıb?',
        topic: 'additional',
        answers: ['Bəli', 'Bəzən', 'Xeyr']
    },
    {
        id: 'm-additional-2',
        text: 'Qeyri-adi saatlarda mesajlar gəlir?',
        topic: 'additional',
        answers: ['Çox tez-tez', 'Bəzən', 'Heç vaxt']
    },
    {
        id: 'm-additional-3',
        text: 'Yeni parollar qoyub və bölüşmür?',
        topic: 'additional',
        answers: ['Həmişə', 'Ara-sıra', 'Heç vaxt']
    },
    {
        id: 'm-additional-4',
        text: 'Yeni hobbisi var, amma sənlə paylaşmır?',
        topic: 'additional',
        answers: ['Tamamilə gizlidir', 'Bəzən danışır', 'Həmişə paylaşır']
    },
    {
        id: 'm-additional-5',
        text: 'Söhbətləriniz daha qısa və səthi olub?',
        topic: 'additional',
        answers: ['Qəti şəkildə', 'Bir az', 'Yox']
    },
    {
        id: 'm-additional-6',
        text: 'Özünü müdafiə etmək üçün səni "çox qısqanc" adlandırır?',
        topic: 'additional',
        answers: ['Tez-tez', 'Bəzən', 'Yox']
    },
    {
        id: 'm-additional-7',
        text: 'Şübhəli "işgüzar səfərlər" başlayıb?',
        topic: 'additional',
        answers: ['Çox vaxt', 'Nadir hallarda', 'Heç vaxt']
    },
    {
        id: 'm-additional-8',
        text: 'Gecələr telefonla pıçıldaşır?',
        topic: 'additional',
        answers: ['Tez-tez', 'Ara-sıra', 'Yox']
    },
    {
        id: 'm-additional-9',
        text: 'Evdə sənə qarşı səbrsiz davranır?',
        topic: 'additional',
        answers: ['Həmişə', 'Bəzən', 'Yox']
    },
    {
        id: 'm-additional-10',
        text: '"Mən belə adam deyiləm" ifadəsini çox vurğulayır?',
        topic: 'additional',
        answers: ['Tez-tez', 'Bəzən', 'Heç vaxt']
    }
];
