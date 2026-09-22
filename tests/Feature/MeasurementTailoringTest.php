<?php
namespace Tests\Feature;
use App\Models\{MeasurementProfile,Product,User};
use App\Support\Measurements\MeasurementConverter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
class MeasurementTailoringTest extends TestCase {
 use RefreshDatabase;
 public function test_inch_conversion_round_trips_to_canonical_centimetres(): void{$this->assertSame('25.40',MeasurementConverter::toCm(10,'in'));$this->assertSame('10.00',MeasurementConverter::fromCm('25.40','in'));}
 public function test_mobile_customer_can_create_reusable_measurement_profile(): void{
  $u=User::factory()->create();Sanctum::actingAs($u);
  $r=$this->postJson('/api/v1/en/measurements/profiles',['name'=>'My Perahan','garment_type'=>'perahan_tunban','display_unit'=>'in','is_default'=>true,'measurements'=>[
   ['code'=>'perahan_tunban_chest','value'=>40],['code'=>'perahan_tunban_shoulder','value'=>18],['code'=>'perahan_tunban_sleeve','value'=>24],['code'=>'perahan_tunban_shirt_length','value'=>42],['code'=>'perahan_tunban_waist','value'=>36],['code'=>'perahan_tunban_trouser_length','value'=>40],
  ]]);
  $r->assertCreated()->assertJsonPath('data.display_unit','in')->assertJsonPath('data.is_default',true)->assertJsonPath('data.measurements.0.unit','in');
  $this->assertDatabaseHas('measurement_profiles',['user_id'=>$u->id,'name'=>'My Perahan']);
 }
 public function test_measurement_validation_uses_definition_ranges(): void{
  $u=User::factory()->create();Sanctum::actingAs($u);
  $this->postJson('/api/v1/en/measurements/profiles',['name'=>'Bad','garment_type'=>'perahan_tunban','display_unit'=>'cm','measurements'=>[
   ['code'=>'perahan_tunban_chest','value'=>20],['code'=>'perahan_tunban_shoulder','value'=>45],['code'=>'perahan_tunban_sleeve','value'=>60],['code'=>'perahan_tunban_shirt_length','value'=>100],['code'=>'perahan_tunban_waist','value'=>90],['code'=>'perahan_tunban_trouser_length','value'=>100],
  ]])->assertUnprocessable();
 }
 public function test_customer_cannot_update_another_users_profile(): void{
  $owner=User::factory()->create();$other=User::factory()->create();$profile=MeasurementProfile::create(['uuid'=>(string)\Illuminate\Support\Str::uuid(),'user_id'=>$owner->id,'name'=>'Private','garment_type'=>'dress','display_unit'=>'cm']);
  Sanctum::actingAs($other);
  $this->putJson('/api/v1/en/measurements/profiles/'.$profile->uuid,['name'=>'Changed','garment_type'=>'dress','display_unit'=>'cm','measurements'=>[]])->assertNotFound();
 }
 public function test_mobile_customer_can_create_tailoring_request_from_owned_profile(): void{
  $u=User::factory()->create();Sanctum::actingAs($u);
  $profile=MeasurementProfile::create(['uuid'=>(string)\Illuminate\Support\Str::uuid(),'user_id'=>$u->id,'name'=>'Dress','garment_type'=>'dress','display_unit'=>'cm']);
  $this->postJson('/api/v1/en/tailoring/requests',['product_slug'=>'hand-embroidered-afghan-dress','measurement_profile_uuid'=>$profile->uuid,'notes'=>'Wedding fit'])->assertCreated()->assertJsonPath('data.status','ready');
  $this->assertDatabaseHas('tailoring_requests',['user_id'=>$u->id,'status'=>'ready']);
 }
}