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

    Route::get('centers', [CentersController::class, 'show']);
    Route::post('centers', [CentersController::class, 'store']);
    
    Route::get('payments', [PaymentsController::class, 'show']);
    Route::get('pending-payments', [PaymentsController::class, 'pendingPayments']);
    Route::put('confirm-payment', [PaymentsController::class, 'confirmPayment']);
    Route::get('proof-of-payment/{other_reference}', [PaymentsController::class, 'fetchProof']);

    Route::get('my-payments', [PaymentsController::class, 'myPayments'])->name('my-payments');
    
    Route::get('/verified-payments', [PaymentsController::class, 'verifiedPayments']);
    Route::post('store-payment', [PaymentsController::class, 'storePayment']);
    Route::post('/manual-payment', [PaymentsController::class, 'storeManualPayment']);
    Route::get('/my-courses', [PaymentsController::class, 'registeredCourses'])->name('payment.my-courses');
    Route::get('/my-registerable-courses', [CourseListController::class, 'showMyRegisterableCourses'])->name('payment.my-registerable-courses');
    

    Route::get('states', [StatesController::class, 'show']);
    Route::post('/generate-receipt', [PdfController::class, 'generateReceipt']);

    Route::get('my-admissions/{client_id}', [AdmissionController::class, 'myAdmissions']);
    Route::get('admissions', [AdmissionController::class, 'show']);
    Route::get('admitted-clients', [AdmissionController::class, 'admittedClients'])->name('admissions.admitted');
    
    Route::get('admitted', [AdmissionController::class, 'show'])->name('admissions.admitted');
    

    Route::put('admissions/{admission_number}', [AdmissionController::class, 'approval'])->name('process-admissions');
    Route::put('admit-all', [AdmissionController::class, 'admitAll']);
    Route::post('admissions/admission_letter', [PdfController::class, 'generateAdmissionLetter'])->name('admission_letter');
    
    Route::post('certificates/client-certificate/{admission_number}', [PdfController::class, 'downloadCertificate'])->name('download_certificate');
    Route::post('certificate/issue', [PdfController::class, 'generateCertificate'])->name('certificate');
    Route::get('certificates', [CertificatesController::class, 'clientCertificates'])->name('certificate');
    Route::post('certificates/batch-process', [CertificatesController::class, 'batchProcess']);
    Route::get('process_certificate', [AdmissionController::class, 'processCertificate']);
    
    Route::post('certificates/client-certificate/download/{admission_number}', [CertificatesController::class, 'downloadCertificate']);
    Route::post('certificates/client-certificate/email/{admission_number}', [CertificatesController::class, 'emailCertificate']);
    Route::get('certificates/my-certificates/{client_id}', [CertificatesController::class, 'myCertificates']);
    
    
    Route::get('get-role', [AuthController::class, 'getRole']);
    Route::get('statistics', [ClientController::class, 'statistics']);

    Route::put('change-password', [AuthController::class, 'changePassword'])->name('change-password');

    
    Route::post('/upload-document', [UploadDocumentController::class, 'uploadDocument']);
    Route::post('/proof-of-payment', [PaymentsController::class, 'uploadProofOfPayment']);

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

Route::get('/verify-payment', [PdfController::class, 'verify'])->name('pdf.verify');
Route::get('/verify_certificate', [PdfController::class, 'verifyCertificate'])->name('pdf.verify_certificate');

Route::get('/qr-codes', [PdfController::class, 'generate']);

Route::post('forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [AuthController::class, 'reset']);
// Auth::routes(['verify' => true]);