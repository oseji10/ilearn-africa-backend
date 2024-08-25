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

    Route::get('centers', [CentersController::class, 'show']);
    Route::post('centers', [CentersController::class, 'store']);
    
    Route::get('payments', [PaymentsController::class, 'show']);
    Route::get('pending-payments', [PaymentsController::class, 'pendingPayments']);
    Route::put('confirm-payment', [PaymentsController::class, 'confirmPayment']);
    
    Route::get('my-payments', [PaymentsController::class, 'myPayments'])->name('my-payments');
    
    Route::get('/verified-payments', [PaymentsController::class, 'verifiedPayments']);
    Route::post('store-payment', [PaymentsController::class, 'store']);
    Route::post('/manual-payment', [PaymentsController::class, 'storeManualPayment']);
    Route::get('/my-courses', [PaymentsController::class, 'registeredCourses'])->name('payment.my-courses');
    Route::get('/my-registerable-courses', [CourseListController::class, 'showMyRegisterableCourses'])->name('payment.my-registerable-courses');
    

    Route::get('states', [StatesController::class, 'show']);
    Route::post('/generate-receipt', [PdfController::class, 'generateReceipt']);

    Route::get('admissions', [AdmissionController::class, 'show']);
    Route::get('admitted', [AdmissionController::class, 'admittedClients'])->name('admissions.admitted');
    
    Route::put('admissions/{admission_number}', [AdmissionController::class, 'approval'])->name('process-admissions');
    Route::post('admissions/admission_letter', [PdfController::class, 'generateAdmissionLetter'])->name('admission_letter');
    
    Route::post('certificates', [PdfController::class, 'generateCertificate'])->name('certificate');
    Route::get('certificates/my-certificates/{client_id}', [CertificatesController::class, 'myCertificates']);
    
    
    Route::get('get-role', [AuthController::class, 'getRole']);
    Route::get('statistics', [ClientController::class, 'statistics']);

    Route::put('change-password', [AuthController::class, 'changePassword'])->name('change-password');

    
    Route::post('/upload-document', [UploadDocumentController::class, 'uploadDocument']);
    Route::post('/proof-of-payment', [PaymentsController::class, 'uploadProofOfPayment']);
    
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
Route::get('/qr-codes', [PdfController::class, 'generate']);