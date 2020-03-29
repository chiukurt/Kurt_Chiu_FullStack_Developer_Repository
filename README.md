# Kurt_Chiu_FullStack_Developer_Repository


## Web application for Prepr Full Stack Challenge using Laravel Framework


Author: Kurt Chiu


Github: https://github.com/chiukurt

Project Link: https://github.com/chiukurt/Kurt_Chiu_FullStack_Developer_Repository

Youtube demonstration: https://www.youtube.com/watch?v=j8onakEmMYQ

LinkedIn profile: https://www.linkedin.com/in/kurt-chiu-a84324198/
<hr>


## Imports external to Laravel:


* MaatWebsite : Used for excel sheet importing


<hr>


## Programming Process


### 1. User Types


* Changed login field of email to username as well as added username field in registration.


        public function username(){
          return 'username';
        }  
        
        

* Added 'role' column to 'users' to differentiate 'admin' roles.


        protected $fillable = [
                'name', 'email', 'password', 'username', 'role'
        ];
        
* ..

         public function up()
            {
                Schema::create('users', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('email')->unique();
                    $table->timestamp('email_verified_at')->nullable();
                    $table->string('username')->unique();
                    $table->string('password');
                    $table->rememberToken();
                    $table->timestamps();
                    $table->string('role')->nullable();
                });
          }

### 2. User privilages (invisible options, forbidden pages)

*  Added an admin role verification to the User.php file

         public function hasRole($role){
                $currentUser = $this->getAttributes();
                if (!is_null($currentUser["role"]) && $currentUser["role"] === 'admin' ){
                    return true;
                }
                return false;
            }



* Defined an authorization gate check using the above function - AuthServiceProvider

        public function boot()
            {
                $this->registerPolicies();
                Gate::define('drop_pins', function($user){
                    return $user->hasRole('admin');
                });
                //
            }




* Added a "Manage Pins" dropdown option from the top right menu only visible if the 'admin' gate authorization passes - app.blade.php

         @can('drop_pins')
              <a class="dropdown-item" href="{{ route('users.index') }}">
                Manage Pins
              </a>
         @endcan



*  In addition to this, the following built in code snippet would give a 403 forbidden error even if the user has the url. Also added a Route with a middleware check of ('can:drop_pins') 


         public function __construct(){
                $this->middleware('auth');
         }


### 3. Seeding users database

* Created database seeds with fields. Registration and user database importing was outside of scope. Null 'role' would act as non-admin nonetheless so regular users can register as normal.


        class UsersTableSeeder extends Seeder
        {
            /**
             * Run the database seeds.
             *
             * @return void
             */
            public function run()
            {
                User::truncate();

                $admin = User::create([
                    'name' => 'Lab Manager [Admin testing account]',
                    'username' => 'labmanager',
                    'password' => Hash::make('password'),
                    'email' => 'lab@manager.com',
                    'role' => 'admin'
                ]);
                $user = User::create([
                    'name' => 'User [User testing account]',
                    'username' => 'user',
                    'password' => Hash::make('password'),
                    'email' => 'user@user.com',
                    'role' => 'user'
                ]);
            }
        }
        
 
### 4. Create database for pin dropping

* The function within the Migration

        public function up()
          {
              Schema::create('pin_imports', function (Blueprint $table) {
                  $table->id();
                  $table->string('title');
                  $table->string('category');
                  $table->string('privacy');
                  $table->float('latitude');
                  $table->float('longitude');
                  $table->string('address');
                  $table->string('city');
                  $table->string('country');
                  $table->timestamps();
              });
          }


* Importing an Excel file from the uploaded file using the built in validator and maat Excel to import the validated file in to the database -  Admin/UsersController.php 

         public function importExcel(Request $request)
            {
                $validator = Validator::make($request->all(), [
                    'file' => 'required|max:99999|mimes:xlsx,ods,xls,csv'
                ]);

                if ($validator->passes()) {
                    //storage/app/spreadsheet
                    $file = $request->file('file');
                    $utime = strtotime('now');
                    $ext = $file->getClientOriginalExtension();
                    $filename = 'pins' . $utime . '.' . $ext;
                    $request->file('file')->storeAs('/public/spreadsheet', $filename);

                    DB::table('pin_imports')->truncate();
                    Excel::import(new PinsImport, '/public/spreadsheet/' . $filename);

                    return redirect()->back()
                        ->with(['success' => 'Successfully updated pins with: ' . $filename]);
                } else {
                    return redirect()->back()
                        ->with(['errors' => $validator->errors()->all()]);
                }
            }
            
            
### 5. Displaying the Lab Locations

* Pass a history of files that have been uploaded using ->with - WIP add download/timestamps - UsersController.php

     public function index()
        {
            $file_list = [];
            if ($handle = opendir(storage_path('app/public/spreadsheet'))) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        $file_list [] =  $file;
                    }
                }
                closedir($handle);
            }

            return view('admin.pins.index')->with('file_list',$file_list);
        }


* Display any errors in the file in the corresponding blade admin view as well as a list the file upload history


                         @if (session('errors'))
                           @foreach ($errors as $error)
                                    <li>{{$error}}</li>
                                @endforeach
                            @endif
                            @if (session('success'))
                                {{session('success')}}
                           @endif
                           
                           
...


                    <h1>List of files:</h1>
                        <ul>
                            @foreach ($file_list as $file)
                                <li>
                                    {{$file}}
                                </li>
                            @endforeach
                        </ul>
                        

* Queries the pin_imports table for Lab Locations within HomeController.php. Using ->with, $arr is passed as $pins below.

         public function index()
            {
                $pins = DB::select('select * from pin_imports');
                $arr = [];
                foreach ($pins as $row){
                    $arr[]=(array)$row;
                }

                return view('home')->with('pins',$arr);
            }


* The table in the view in home.blade.php that displays the final information. Google maps is queried using latitude and longitude. 


                      <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Privacy</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>Country</th>
                                <th>Google Maps</th>
                            </tr>
                            </thead>
                            <tbody>
                            @for ($i = 1 ; $i < sizeof($pins); $i++)
                                <tr>
                                    <td>{{$pins[$i]['title']}}</td>
                                    <td>{{$pins[$i]['category']}}</td>
                                    <td>{{$pins[$i]['privacy']}}</td>
                                    <td>{{$pins[$i]['address']}}</td>
                                    <td>{{$pins[$i]['city']}}</td>
                                    <td>{{$pins[$i]['country']}}</td>
                                    <td>
                                        <a href="{{ 'https://maps.google.com?q='
                                            .$pins[$i]['latitude'].','.$pins[$i]['longitude']}}"
                                           target="_blank">View Google Maps</a>
                                    </td>
                                </tr>
                            @endfor
                            </tbody>
                        </table>



<hr>


## Things I wanted to add


* Dynamic pinning using Google Maps API drag and drop
* Record drops dynamically in server database using insert statements
* Sync pins with google sheets without file uploads
* Draw directly from google sheets using credentials (Could only get it to work with fully public sheets)

         "require": {
            "asimlqt/php-google-spreadsheet-client": "3.0.*"
          }


* The code that worked with google's sample sheets but not with the given lab locations sheet (Change $spreadSheetId and $range)
         
         require '../vendor/autoload.php';

        $client = new \Google_Client();
        $client->setApplicationName("Test");
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS_READONLY]);
        $client->setAccessType('offline');
        try {
            $client->setAuthConfig('../credentials.json');
        } catch (\Google_Exception $e) {
            echo $e;
        }
        $service = new \Google_Service_Sheets($client);
        $spreadSheetId = "1XjqJ7kp2AcMQjWaf_exDTTx-1qU4ctGp";

        $range = "Coworker!A2:Z";
        $response = $service->spreadsheets_values->get($spreadSheetId,$range);
        $values = $response->getValues();
        foreach ($values as $row){
            foreach ($row as $item){
                echo $item." ";
            }
            echo '<br>';
        }


<hr>

## Final thoughts
Enjoyable learning experience. I had to teach myself Laravel within the 2~3 day timeframe for the project.
Had prior experience of MySQL and Oracle SQL but took the opportunity to use sqlite.
Neglected sleeping.
