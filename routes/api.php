<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\QualificationsController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\StatesController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TokenValidationController;
use App\Http\Controllers\GradesController;
use App\Http\Controllers\CourseListController;
use App\Http\Controllers\CentersController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\QrCodeGeneratorController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\UploadDocumentController;
use App\Http\Controllers\CertificatesController;
use App\Http\Controllers\CourseMaterialController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\CohortsController;
use App\Http\Controllers\CBTController;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Route::post('logout', [AuthController::class, 'logout']);
    
    // // Add other routes that require authentication here
    // Route::get('clients', [ClientController::class, 'show']);
    // Route::post('clients', [ClientController::class, 'store']);
    // Route::put('/clients/{id}', [ClientController::class, 'update']);

    // Route::get('courses', [CoursesController::class, 'show']);
    // Route::post('courses', [CoursesController::class, 'store']);

    // Route::get('qualifications', [QualificationsController::class, 'show']);
    // Route::post('qualifications', [QualificationsController::class, 'store']);

    // Route::get('admin', [AdminController::class, 'show']);
    // Route::post('admin', [AdminController::class, 'store']);

    // Route::get('states', [StatesController::class, 'show']);

    Route::post('/validate-token', [TokenValidationController::class, 'validateToken']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    
    // Add other routes that require authentication here
    Route::get('clients', [ClientController::class, 'show']);
    Route::get('/clients/{client_id}', [ClientController::class, 'getClient']);
    Route::delete('delete_client/{client_id}', [ClientController::class, 'deleteClient']);
    
    Route::post('clients', [ClientController::class, 'store']);
    Route::put('/clients/{client_id}', [ClientController::class, 'update']);
    Route::patch('update_client/{client_id}', [ClientController::class, 'updateClient']);

    Route::get('courses', [CoursesController::class, 'show']);
    Route::post('courses', [CoursesController::class, 'store']);
    
    Route::post('qualifications', [QualificationsController::class, 'store']);
    Route::get('qualifications', [QualificationsController::class, 'show']);
    
    Route::post('grades', [GradesController::class, 'store']);
    Route::get('grades', [GradesController::class, 'show']);
    
    Route::get('admin', [AdminController::class, 'show']);
    Route::post('admin', [AdminController::class, 'store']);
    
    Route::get('course_list', [CourseListController::class, 'show']);
    Route::post('course_list', [CourseListController::class, 'store']);
    Route::post('course_material', [CourseMaterialController::class, 'uploadCourseMaterial']);
    Route::delete('delete_course/{course_id}', [CourseListController::class, 'deleteCourse']);
    Route::patch('update_course/{course_id}', [CourseListController::class, 'updateCourse']);
    Route::post('course/module', [CourseListController::class, 'uploadCourseModule']);
    Route::get('course/modules/{course_id}', [CourseListController::class, 'getCourseModules']);
    Route::patch('course/module/{id}', [CourseListController::class, 'updateModule']);
    Route::delete('course/module/{id}', [CourseListController::class, 'deleteModule']);
    
    Route::get('centers', [CentersController::class, 'show']);
    Route::post('centers', [CentersController::class, 'store']);
    Route::patch('centers/update/{center_id}', [CentersController::class, 'updateCenter']);
    
    Route::get('cohorts-courses', [CoursesController::class, 'cohortCourses']);
    Route::get('cohorts', [CohortsController::class, 'showCohorts']);
    Route::get('cohorts/courses', [CohortsController::class, 'showCohortsAllcourses']);
    Route::get('cohorts/{cohort_id}/courses', [CohortsController::class, 'showCohortsCourses']);
    
    
    Route::post('cohorts/add-cohort', [CohortsController::class, 'addCohort']);
    Route::post('cohorts/add-cohort-courses', [CohortsController::class, 'addCohortCourses']);
    Route::patch('cohorts/update-cohort/{cohort_id}', [CohortsController::class, 'updateCohort']);
    Route::delete('cohorts/delete-cohort/{cohort_id}', [CohortsController::class, 'deleteCohort']);
    

    Route::get('cohorts/active-cohorts', [CohortsController::class, 'activeCohorts']);
    Route::patch('cohorts/change-cohort-status', [CohortsController::class, 'changeCohortStatus']);
    
    Route::get('payments', [PaymentsController::class, 'show']);
    Route::get('pending-payments', [PaymentsController::class, 'pendingPayments']);
    Route::get('pending-part-payments', [PaymentsController::class, 'pendingPartPayments']);
    
    Route::get('rejected-payments', [PaymentsController::class, 'rejectedPayments']);
    Route::post('confirm-payment', [PaymentsController::class, 'confirmPayment']);
    Route::put('reject-payment', [PaymentsController::class, 'rejectPayment']);
    Route::get('proof-of-payment/{other_reference}', [PaymentsController::class, 'fetchProof']);

    Route::get('my-payments', [PaymentsController::class, 'myPayments'])->name('my-payments');
    Route::post('top-up-payment', [PaymentsController::class, 'topUpPayment']);
    Route::get('my-part-payment-history/{id}', [PaymentsController::class, 'myPaymentPartPaymentHistory']);
    
    Route::get('/verified-payments', [PaymentsController::class, 'verifiedPayments']);
    Route::post('store-payment', [PaymentsController::class, 'storePayment']);
    Route::post('/manual-payment', [PaymentsController::class, 'storeManualPayment']);
    Route::get('/my-courses', [PaymentsController::class, 'registeredCourses'])->name('payment.my-courses');
    // Route::get('/my-registerable-courses', [CourseListController::class, 'showMyRegisterableCourses'])->name('payment.my-registerable-courses');
    
    Route::get('/my-registerable-courses/{cohort_id}', [CourseListController::class, 'showMyRegisterableCourses'])->name('payment.my-registerable-courses');
    

    Route::get('states', [StatesController::class, 'show']);
    Route::post('/generate-receipt', [PdfController::class, 'generateReceipt']);

    Route::get('my-admissions/{client_id}', [AdmissionController::class, 'myAdmissions']);
    Route::get('admissions', [AdmissionController::class, 'show']);
    Route::get('admitted-clients', [AdmissionController::class, 'admittedClients'])->name('admissions.admitted');
    
    Route::get('admitted', [AdmissionController::class, 'show'])->name('admissions.admitted');
    

    Route::put('admissions/{admission_number}', [AdmissionController::class, 'approval'])->name('process-admissions');
    Route::put('admit-all', [AdmissionController::class, 'admitAll']);
    Route::post('admissions/admission_letter/download', [PdfController::class, 'generateAdmissionLetter']);
    Route::post('admissions/admission_letter/email', [PdfController::class, 'emailAdmissionLetter']);
    
    Route::post('certificates/client-certificate/{admission_number}', [PdfController::class, 'downloadCertificate'])->name('download_certificate');
    Route::post('certificate/issue', [PdfController::class, 'generateCertificate'])->name('certificate');
    Route::get('certificates', [CertificatesController::class, 'clientCertificates'])->name('certificate');
    Route::post('certificates/batch-process', [CertificatesController::class, 'batchProcess']);
    Route::get('process_certificate', [AdmissionController::class, 'processCertificate']);
    
    Route::post('certificate/ilearn/issue', [PdfController::class, 'issueIlearnAfricaCertificate'])->name('ilearn_certificate');

    
    Route::post('certificates/client-certificate/download/{admission_number}', [CertificatesController::class, 'downloadCertificate']);
    Route::post('certificates/client-certificate/email/{admission_number}', [CertificatesController::class, 'emailCertificate']);
    Route::get('certificates/my-certificates/{client_id}', [CertificatesController::class, 'myCertificates']);
    
    
    Route::get('get-role', [AuthController::class, 'getRole']);
    Route::get('statistics', [ClientController::class, 'statistics']);

    Route::put('change-password', [AuthController::class, 'changePassword'])->name('change-password');

    
    Route::post('/upload-document', [UploadDocumentController::class, 'uploadDocument']);
    Route::post('/proof-of-payment', [PaymentsController::class, 'uploadProofOfPayment']);
    Route::post('/edit_payment', [PaymentsController::class, 'updateAmount']);

    Route::get('/course_materials', [CourseMaterialController::class, 'showMaterials']);
    Route::post('/upload-profile-image', [ClientController::class, 'profileImage']);

    Route::get('incomplete_applications', [StatisticsController::class, 'incompleteApplications']);
    Route::get('registered_clients', [StatisticsController::class, 'registeredClients']);
    Route::get('pending_admissions', [StatisticsController::class, 'pendingAdmissions']);
    Route::get('currently_admitted', [StatisticsController::class, 'currentlyAdmitted']);
    Route::get('graduated', [StatisticsController::class, 'graduated']);
    
    Route::get('payments_today', [StatisticsController::class, 'paymentsToday']);
    Route::get('payments_this_week', [StatisticsController::class, 'paymentsThisWeek']);
    Route::get('payments_this_month', [StatisticsController::class, 'paymentsThisMonth']);
    Route::get('all_payments', [StatisticsController::class, 'allPayments']);
    

});

Route::options('/{any}', function () {
    return response('', 200);
})->where('any', '.*');

Route::middleware('auth:sanctum')->get('/test-token', function (Request $request) {
    return response()->json(['message' => 'Token is valid.']);
});

Route::middleware('auth:sanctum')->get('/client-id', [AuthController::class, 'getClientId']);

// In routes/web.php

Route::get('/verify-payment-receipt', [PdfController::class, 'verify'])->name('pdf.verify');
Route::get('/verify_certificate', [PdfController::class, 'verifyCertificate'])->name('pdf.verify_certificate');
Route::post('/download_receipt', [PdfController::class, 'downloadReceipt'])->name('pdf.downloadReceipt');


Route::get('/qr-codes', [PdfController::class, 'generate']);

Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [AuthController::class, 'reset']);
// Auth::routes(['verify' => true]);

Route::post('cohorts/delete-cohort-course', [CohortsController::class, 'deleteCohortCourse']);

Route::post('/initialize-payment', [PaymentsController::class, 'initializePayment']);
Route::get('/verify-payment', [PaymentsController::class, 'verifyAndStorePayment']);
Route::get('/verify-this-payment', [PaymentsController::class, 'verifyAndStorePaymentForiLearnCoursesOnly']);
Route::get('/verify-this-payment-dashboard', [PaymentsController::class, 'verifyAndStorePaymentForiLearnCoursesOnlyOnDashboard']);
Route::post('/notify-payment', [PaymentsController::class, 'notifyPayment']);
Route::post('/notify-payment2', [PaymentsController::class, 'notifyPayment2']);

Route::post('/check-user', [AuthController::class, 'checkUser']);


// CBT Routes
Route::get('cbt-exams', [CBTController::class, 'RetrieveAll']);
Route::post('cbt-exams/{examId}/clone', [CBTController::class, 'cloneCBT']);
Route::post('cbt-exams/retake', [CBTController::class, 'retakeExam']);
Route::post('cbt-exams', [CBTController::class, 'store']);
Route::put('cbt-exams/{examId}', [CBTController::class, 'updateCBT']);
Route::get('cbt-exams/questions', [CBTController::class, 'RetrieveAllQuestions']);
Route::get('cbt-exams/questions/{examId}', [CBTController::class, 'RetrieveExamQuestions']);
Route::post('cbt-exams/question', [CBTController::class, 'storeQuestion']);
Route::put('cbt-exams/question/{questionId}', [CBTController::class, 'updateQuestion']);
Route::delete('cbt-exams/question/{questionId}', [CBTController::class, 'deleteQuestion']);
Route::get('my-cbt-exams', [CBTController::class, 'RetrieveCBT']);
Route::get('client-cohort/{client_id}', [CBTController::class, 'RetrieveClientWithCohort']);

Route::get('cohort/{cohort_id}/clients', [CohortsController::class, 'cohortsClients']);
Route::put('clients/{client_id}/update-cohort', [CohortsController::class, 'updateClientCohort']);

Route::get('questions/{examId}', [CBTController::class, 'loadQuestions']);
Route::post('exam-result', [CBTController::class, 'submitExam']);
Route::get('exam-result/{examId}', [CBTController::class, 'ExamResults']);
Route::post('cbt-exam-result', [CBTController::class, 'MyExamResult']);
Route::get('my-cbt-exam-results/{client_id}', [CBTController::class, 'MyCBTExamResult']);
Route::get('examination-results', [CBTController::class, 'CBTExamResults']);
Route::get('/detailed-exam-results/{masterId}', [CBTController::class, 'getUserExamResults']);

Route::delete('cbt-exam-result/{masterId}', [CBTController::class, 'deleteStudentExamResult']);

Route::get('/test_result/{masterId}', [CBTController::class, 'downloadExamResults']);
Route::get('/test_result', function () {
    return view('pdf.test_report', [
        'candidate_number' => '24NG501458IDUE448G',
        'centre_number' => '22/JUL/2024NG448',
        'first_name' => 'ESEOSA',
        'surname' => 'IDUSERI',
        'dob' => '25/07/1988',
        'country_origin' => 'NIGERIA',
        'country_nationality' => 'NIGERIA',
        'first_language' => 'ENGLISH',
        'listening_score' => 8.0,
        'reading_score' => 6.5,
        'writing_score' => 5.0,
        'speaking_score' => 6.0,
        'overall_band_score' => 6.5,
        'cefr_level' => 'B2',
        'test_date' => '24/07/2024'
    ]);
});

Route::post('/upload-questions', [CBTController::class, 'importQuestions']);

Route::get('courses/active', [CoursesController::class, 'activeCourses']);

Route::post('certificates/client-certificate/test/{admission_number}', [PdfController::class, 'downloadCertificate'])->name('download_certificate');