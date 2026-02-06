<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Validation\Rule;
use App\Http\Requests\Api\BaseRequest;
use App\Models\PasswordSetting;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Validation\Rules\Password;
use App\Rules\Uppercase;

class StoreRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $company = company();
        $passwordSettings = PasswordSetting::where('password_settings.company_id',$company->id)->first();
      
        $loggedUser = user();

        $rules = [
            'phone'    => [
                'numeric',
                Rule::unique('users', 'phone')->where(function ($query) {
                    return $query->where('user_type', 'staff_members')
                        ->orWhere('user_type', 'super_admins');
                })
            ],
            'status' => 'required',
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users', 'email')
                    ->where(function ($query) {
                        $query->whereIn('user_type', ['staff_members', 'super_admins']);
                    }),
            ],
            'user' => [
                'required',
                Rule::unique('users', 'user')
                    ->where(function ($query) {
                        $query->whereIn('user_type', ['staff_members', 'super_admins']);
                    }),
            ],
        ];

        if ($loggedUser->hasRole('admin')) {
            $rules['role_id'] = 'required';
        }

        $password = $this->password;
        $isNumber = $this->isNumber($password);
        $isUpperCase = $this->isUpperCase($password);
        $isLowerCase = $this->isLowerCase($password);
        $isSymbol = $this->isSymbol($password);
        $passwordLength = strlen($password);
        
        if(isset($passwordSettings)){
            $passSettingLength = $passwordSettings['password_length'];
        }

        if($this->ucontact == 1){
            $rules['ucontact_user'] = [
                'required',
                Rule::unique('users', 'ucontact_user')
                    ->where(function ($query) {
                        $query->whereIn('user_type', ['staff_members', 'super_admins']);
                    }),
            ];
            $rules['ucontact_password'] = 'required';
            $rules['uc_ext'] = [
                'required',
                Rule::unique('users', 'uc_ext')
                    ->where(function ($query) {
                        $query->whereIn('user_type', ['staff_members', 'super_admins']);
                    }),
            ];
        }
      
        if(!$password){
            $rules['password'] = [
                'required',
            ];
        } else {
            if($password && $passwordSettings['upper_case'] == 1 && !$isUpperCase){
                $rules['password'] = [
                    'required',
                    function ($attribute, $value, $fail) {
                            $fail('passwrod must contains one uppercase letter.');
                    },
                ];
            }
    
            if($password && $passwordSettings['lower_case'] == 1 && !$isLowerCase){
                $rules['password'] = [
                    'required',
                    function ($attribute, $value, $fail) {
                            $fail('passwrod must contains one lowercase letter.');
                    },
                ];
            }
    
            if($password && $passwordSettings['special_character'] == 1 && !$isSymbol){
                $rules['password'] = [
                    'required',
                    function ($attribute, $value, $fail) {
                            $fail('passwrod must contains one special character letter.');
                    },
                ];
            }
    
            if($password && $passwordSettings['number'] == 1 && !$isNumber){
                $rules['password'] = [
                    'required',
                    function ($attribute, $value, $fail) {
                            $fail('passwrod must contains one number.');
                    },
                ];
            }
            
            if ($passwordLength < $passSettingLength){
                $rules['password'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($passSettingLength) {
                            $fail('passwrod length must be greater than or equal to'.' '.$passSettingLength);
                    },
                ];
              } 
        }
     
        return $rules;
    }

    public function isNumber($pass)
    {
        return (bool) preg_match('/[0-9]/', $pass);
    }

    public function isUpperCase($pass)
    {
        return (bool) preg_match('/[A-Z]/', $pass);
    }

    public function isLowerCase($pass)
    {
        return (bool) preg_match('/[a-z]/', $pass);
      
    }

    public function isSymbol($pass)
    {
        return (bool) preg_match('/[@$!%*#?&.]/', $pass);
      
    }
  
}
