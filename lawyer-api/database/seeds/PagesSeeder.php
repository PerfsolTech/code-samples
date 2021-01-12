<?php

use App\Models\Faq;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    public function run()
    {
        $this->seedTerms();
        $this->seedPrivacy();
        $this->seedClientFaqs();
        $this->seedLawyerFaqs();
    }

    private function seedTerms()
    {
        Page::create([
            'name' => 'terms',
            'title' => 'Terms & Conditions',
            'body' => file_get_contents(base_path('database/seeds/pages/client/terms.html')),
            'type' => User::TYPE_CLIENT
        ]);
        Page::create([
            'name' => 'terms',
            'title' => 'Terms & Conditions',
            'body' => file_get_contents(base_path('database/seeds/pages/lawyer/terms.html')),
            'type' => User::TYPE_LAWYER
        ]);
    }

    private function seedPrivacy()
    {
        Page::create([
            'name' => 'privacy',
            'title' => 'Ammurapi Privacy Policy',
            'body' => file_get_contents(base_path('database/seeds/pages/client/privacy.html')),
            'type' => User::TYPE_CLIENT
        ]);
        Page::create([
            'name' => 'privacy',
            'title' => 'Ammurapi Business Privacy Policy',
            'body' => file_get_contents(base_path('database/seeds/pages/lawyer/privacy.html')),
            'type' => User::TYPE_LAWYER
        ]);
    }

    private function seedClientFaqs()
    {
        Faq::create([
            'question' => 'What type of service can Ammurapi offer?',
            'answer' => '<p>You will have access to all lawyers available on Ammurapi application worldwide, which will help in any type of legal consultation, opening cases and/or assigning lawyers to cases.</p>',
            'answer_text' => 'You will have access to all lawyers available on Ammurapi application worldwide, which will help in any type of legal consultation, opening cases and/or assigning lawyers to cases.',
            'type' => User::TYPE_CLIENT
        ]);
        Faq::create([
            'question' => 'How can I find the most convenient lawyers for my questions and clarifications?',
            'answer' => '<p>You can filter your search results while doing your search by using the filter functionality. In addition to that, you can sort the results for a better searching experience.</p>
                         <ul>
                             <li>Alphabetically: The lawyers in the list will be sorted alphabetically.</li>
                             <li>By Rating: The lawyers in the list will be sorted by rating.</li>
                             <li>Number of cases taken: The lawyers in the list will be sorted based on the number of cases taken.</li>
                         <ul/>',
            'answer_text' => 'You can filter your search results while doing your search by using the filter functionality. In addition to that, you can sort the results for a better searching experience. Alphabetically: The lawyers in the list will be sorted alphabetically. By Rating: The lawyers in the list will be sorted by rating. Number of cases taken: The lawyers in the list will be sorted based on the number of cases taken.',
            'type' => User::TYPE_CLIENT
        ]);
        Faq::create([
            'question' => 'How can I search for lawyers using the Near-by functionality?',
            'answer' => '<p>You can view your search results by using the toggle menu available under the search field:</p>
                         <ul>
                            <li>List view: This is the default view which displays the resulting lawyers as a list.</li>
                            <li>Near-by: Once selected, this displays the resulting lawyers on the map.</li>
                         <ul/>
                         <p>Note: The same lawyers will be displayed while toggling between views (List view, Near-by)</p>',
            'answer_text' => 'You can view your search results by using the toggle menu available under the search field: List view: This is the default view which displays the resulting lawyers as a list. Near-by: Once selected, this displays the resulting lawyers on the map. Note: The same lawyers will be displayed while toggling between views (List view, Near-by)',
            'type' => User::TYPE_CLIENT
        ]);
        Faq::create([
            'question' => 'Where can I find the communication done with Ammurapi support team?',
            'answer' => '<p>In your inbox.</p>',
            'answer_text' => 'In your inbox.',
            'type' => User::TYPE_CLIENT
        ]);
        Faq::create([
            'question' => 'How to assign a lawyer to a new case?',
            'answer' => '<p>Once you create the case, Ammurapi will be studying the case and accordingly suggesting five lawyers to your case so you can choose your lawyer.</p>
                         <p>We will study every case by case and based on the characteristics (listed below) of the case, the system will suggest the lawyers:</p>
                         <ul>
                            <li>Language</li>
                            <li>Expertise</li>
                            <li>City</li>
                         </ul>
                         <p>Navigate to the case screen and select a lawyer from the Suggested Lawyers section. Click on the lawyer’s profile picture to view their profile where you can find two buttons labeled as Assign and Dismiss.</p>
                         <ul>
                            <li>If you click on Assign: The lawyer will be notified and directly assigned to your case.</li>
                            <li>If you click on Dismiss: The lawyer will not be available to select for the case anymore.</li>
                         </ul>',
            'answer_text' => 'Once you create the case, Ammurapi will be studying the case and accordingly suggesting five lawyers to your case so you can choose your lawyer. We will study every case by case and based on the characteristics (listed below) of the case, the system will suggest the lawyers: Language Expertise City Navigate to the case screen and select a lawyer from the Suggested Lawyers section. Click on the lawyer’s profile picture to view their profile where you can find two buttons labeled as Assign and Dismiss. If you click on Assign: The lawyer will be notified and directly assigned to your case. If you click on Dismiss: The lawyer will not be available to select for the case anymore.',
            'type' => User::TYPE_CLIENT
        ]);
        Faq::create([
            'question' => 'How can I make sure that the displayed lawyers are actual lawyers?',
            'answer' => '<p>Ammurapi checks all lawyers’ accounts before making them available on Ammurapi application. The credibility of the lawyer is confirmed by the system, after checking the lawyer’s Syndicate ID (Bar #). If the lawyer has a valid Bar #, then the lawyer’s account will be activated and users will be able to search this lawyer, else, the system will prevent the lawyer from joining by rejecting the account created.</p>',
            'answer_text' => 'Ammurapi checks all lawyers’ accounts before making them available on Ammurapi application. The credibility of the lawyer is confirmed by the system, after checking the lawyer’s Syndicate ID (Bar #). If the lawyer has a valid Bar #, then the lawyer’s account will be activated and users will be able to search this lawyer, else, the system will prevent the lawyer from joining by rejecting the account created.',
            'type' => User::TYPE_CLIENT
        ]);
        Faq::create([
            'question' => 'How can I rate a Lawyer?',
            'answer' => '<p>Any time after initiating the first communication with the lawyer, you will be able to rate the lawyer from the lawyer’s profile.</p>',
            'answer_text' => 'Any time after initiating the first communication with the lawyer, you will be able to rate the lawyer from the lawyer’s profile.',
            'type' => User::TYPE_CLIENT
        ]);

    }

    private function seedLawyerFaqs()
    {
        Faq::create([
            'question' => 'What will Ammurapi help me do?',
            'answer' => '<p>Ammurapi will increase your visibility by displaying your profile for all users around the world, and will help you manage all your legal practice activity within one application.</p>',
            'answer_text' => 'Ammurapi will increase your visibility by displaying your profile for all users around the world, and will help you manage all your legal practice activity within one application.',
            'type' => User::TYPE_LAWYER
        ]);
        Faq::create([
            'question' => 'Based on what does Ammurapi suggest the lawyers after a case creation?',
            'answer' => '<p>We will study every case by case and based on the characteristics (listed below) of the case, the system will suggest five lawyers:</p>
                         <ul>
                            <li>Language</li>
                            <li>Expertise</li>
                            <li>City</li>
                        </ul>',
            'answer_text' => 'We will study every case by case and based on the characteristics (listed below) of the case, the system will suggest five lawyers: Language Expertise City',
            'type' => User::TYPE_LAWYER
        ]);
        Faq::create([
            'question' => 'How can clients connect to me?',
            'answer' => '<ul>
                            <li>Phone Call</li>
                            <li>Message (Ammurapi Inbox)</li>
                         </ul>',
            'answer_text' => 'Phone Call Message (Ammurapi Inbox)',
            'type' => User::TYPE_LAWYER
        ]);
        Faq::create([
            'question' => 'How can I control the communication between myself and the clients?',
            'answer' => '<p>In the notification screen under settings, you can enable/disable the means of communication available.</p>
                         <ul>
                            <li>Phone Call</li>
                            <li>Message</li>
                         </ul>',
            'answer_text' => 'In the notification screen under settings, you can enable/disable the means of communication available. Phone Call Message',
            'type' => User::TYPE_LAWYER
        ]);
        Faq::create([
            'question' => 'Is there a cost to join or monthly fee to participate?',
            'answer' => '<p>The application is free within the trial period defined below.</p>',
            'answer_text' => 'The application is free within the trial period defined below.',
            'type' => User::TYPE_LAWYER
        ]);
        Faq::create([
            'question' => 'How do I get paid?',
            'answer' => '<p>Financial transactions are done offline, and are based on any normal agreement done between the client and the lawyer, and not through Ammurapi.</p>',
            'answer_text' => 'Financial transactions are done offline, and are based on any normal agreement done between the client and the lawyer, and not through Ammurapi.',
            'type' => User::TYPE_LAWYER
        ]);
        Faq::create([
            'question' => 'Who can rate me?',
            'answer' => '<p>Rating the lawyer can only be done when a communication is done between the client and the lawyer (message).</p>',
            'answer_text' => 'Rating the lawyer can only be done when a communication is done between the client and the lawyer (message).',
            'type' => User::TYPE_LAWYER
        ]);
    }

}