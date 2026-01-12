<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiBaseController;
use App\Http\Requests\Api\User\IndexRequest;
use App\Http\Requests\Api\User\StoreRequest;
use App\Http\Requests\Api\User\UpdateRequest;
use App\Http\Requests\Api\User\DeleteRequest;
use App\Models\User;
use App\Traits\UserTraits;

class UsersController extends ApiBaseController
{
    use UserTraits;

    protected $model = User::class;

    protected $indexRequest = IndexRequest::class;
    protected $storeRequest = StoreRequest::class;
    protected $updateRequest = UpdateRequest::class;
    protected $deleteRequest = DeleteRequest::class;

    public function __construct()
    {
        parent::__construct();

        $this->userType = "staff_members";
    }

    public function destroy(...$args)
    {
        \DB::beginTransaction();

        $xid = last($args);
        $id  = \Vinkla\Hashids\Facades\Hashids::decode($xid)[0];

        // Validación del request delete
        $this->validate();

        /** @var \Illuminate\Database\Eloquent\Model $obj */
        $this->setQuery(call_user_func($this->model . '::query'));
        // $this->modify(); // respeta filtros/tenant si los hubiera
        $obj = $this->getQuery()->findOrFail($id);

        $obj->status = 'disabled';
        $obj->save();

        \DB::commit();

        return \Examyou\RestAPI\ApiResponse::make(
            'Resource inactivated instead of deleted',
            ['xid' => $obj->xid],
            $this->getMetaData(true)
        );
    }
}
