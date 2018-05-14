<?php

namespace App\Controller;

use Cake\Datasource\ConnectionInterface;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;
use Cake\Network\Session;

/**
 * Products Controller
 *
 * @property \App\Model\Table\ProductsTable $Products
 */
class ProductsController extends AppController {

    public function beforeFilter(Event $event) {
        parent::beforeFilter($event);
        $this->Auth->allow(['products_list','dipping_price','dippingsauce_details','refreshcombocart','refreshcomboprice',
		'final_combo_cart','drink_price','party_view','special_details','addon_price','special_total_price', 'refresh_deals',
		'drink_details', 'refresh_flavours', 'refresh_toppings', 'game_details', 'refresh_wings', 'platter_details', 
		'party_toppings', 'party_price', 'party_details', 'platter_sauce', 'platter_price', 'extra_details', 'wings_cart',
		'total_price', 'addons', 'more_details', 'size_calculate', 'combo_products_view', 'quick_cart', 'pizza_index',
		'index', 'view', 'combo_products', 'product_list_all', 'pizza_view', 'add', 'edit', 'delete', 'add_in_cart', 
		'cart_products_addons', 'pizza_index', 'sub_addons', 'sub_category', 'cart', 'price_calculate', 'products_details', 
		'calculate', 'products_view', 'menu', 'products_addons', 'special_view', 'pizza_combo',
		'appatizer_details','salad_products_view','salad_extra_details','pizza_calculate','get_increment','app_calculate',
		'combo_calculate','combo_drink_price','combo_dip_price','topping_price','combo_side_price','pizza_calculate_yours',
		'final_combo_update_cart','make_start_price','salad_total_price','special_pizza_calculate','quick_add',
		'combo_lasagana_calculate','test_details','item_details','products_addon_list','get_addon_details','get_addon_categories',
		'get_addon_subcategories','get_product_price','get_size_category','get_product_direct_cat','calculate_total_price',
        '_GetFreeAddonList','_GetAddonPrice','create_cart','_CartTotalPrice','get_addon_order','pizza_calculate_appatizer','special_pizza_calculate_yours', 
        'topping_price_save','toppings_add', 'toppings_edit']);
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index() {
        if ($this->check_admin_login() != 1) {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
        $Categories = TableRegistry::get('Categories');
        $categories = $Categories->find("all")
                ->where(['parent_id' => PIZZA_ID]);
        $categoryArr = array();
        foreach ($categories as $key => $value) {
            $categoryArr[] = $value['id'];
        }
        // $products = $this->Products->find()
        // ->where(['Products.category_id  NOT IN' =>$categoryArr])
        // ->all();

        $this->layout = 'admin';
        $this->paginate = [
            'contain' => ['Categories']
        ];
        $session = new Session();

        $sessionNewData = $session->read('Config.adminLoc');
        $Categories = TableRegistry::get('Categories');

        $categories_list = $Categories->find("all");
        foreach ($categories_list as $key => $value) {
            $categories_listArr[$value['id']] = $value['name'];
        }
        if(isset($sessionNewData))
        {
            $this->set('products', $this->Paginator->paginate($this->Products, ['limit' => 10, 'conditions' => ['Products.restaurants_id'=>$sessionNewData,'Products.category_id  NOT IN' => $categoryArr]]));

        }
        else
        {
           $this->set('products', $this->Paginator->paginate($this->Products, ['limit' => 10, 'conditions' => ['Products.category_id  NOT IN' => $categoryArr]])); 
        }
        $this->set('categories_listArr', $categories_listArr);
        
		//$this->set('products', $this->Paginator->paginate($this->Products, ['limit' => 10, 'conditions' => ['Products.category_id ' => 45]]));
        $this->set('_serialize', ['products']);
    }

    public function pizza_index() {
        if ($this->check_admin_login() != 1) {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
        $categories_listArr = array();
        $Categories = TableRegistry::get('Categories');
        $categories_list = $Categories->find("all");
        foreach ($categories_list as $key => $value) {
            $categories_listArr[$value['id']] = $value['name'];
        }
        $this->set('categories_listArr', $categories_listArr);


        $categories = $Categories->find("all")
                ->where(['parent_id' => PIZZA_ID]);
        $categoryArr = array();
        foreach ($categories as $key => $value) {
            $categoryArr[] = $value['id'];
        }
        $products = $this->Products->find()
                ->where(['Products.category_id  IN' => $categoryArr])
                ->all();

        $this->layout = 'admin';
        $this->paginate = [
            'contain' => ['Categories']
        ];

        $this->set('products', $this->Paginator->paginate($this->Products, ['limit' => 10, 'conditions' => ['Products.category_id IN' => $categoryArr]]));

        //$this->set('products', $products);
        $this->set('_serialize', ['products']);
    }

    /**
     * View method
     *
     * @param string|null $id Product id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null) {
        if ($this->check_admin_login() != 1) {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
        $this->layout = 'admin';
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Addons']
        ]);
        $this->set('product', $product);
        $this->set('_serialize', ['product']);



        $Categories = TableRegistry::get('Categories');
        $Parent_categories = $Categories->find("all")
                ->where(['parent_id' => 0]);
        $this->set('Parent_categories', $Parent_categories);

        $categories1 = $Categories->find("all");

        $this->set('categories1', $categories1);
        $categories = $this->Products->Categories->find('list', ['limit' => 200]);
        $this->set(compact('product', 'categories'));
        $this->set('_serialize', ['product']);

        $AddonCategories = TableRegistry::get('AddonCategories');

        $addonCategories = $AddonCategories->find("all")
                ->where(['parent_id' => 0]);
        $this->set('addonCategories', $addonCategories);

        $Addons = TableRegistry::get('Addons');
        $addons = $Addons->find("all");
        $this->set('addons', $addons);

        $ProductAddons = TableRegistry::get('ProductAddons');
        $productAddons = $ProductAddons->find("all")
                ->where(['product_id' => $id]);
        $this->set('productAddons', $productAddons);

        $Categories = TableRegistry::get('Categories');

        $categories_list = $Categories->find("all");
        foreach ($categories_list as $key => $value) {
            $categories_listArr[$value['id']] = $value['name'];
        }
        $this->set('categories_listArr', $categories_listArr);
    }

    public function pizza_view($id = null) {
        if ($this->check_admin_login() != 1) {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
        $this->layout = 'admin';
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Addons']
        ]);
        $this->set('product', $product);
        $this->set('_serialize', ['product']);

        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find("all")
                ->where(['product_id' => $id]);
        $this->set('product_addons', $product_addons);

        $Addons = TableRegistry::get('Addons');
        $addons_all = $Addons->find("all");
        $this->set('addons_all', $addons_all);

        $AddonCategories = TableRegistry::get('AddonCategories');
        $addon_categories = $AddonCategories->find("all");
        $this->set('addon_categories', $addon_categories);

        // $pizzaCatArr=array(DIPS_ID,TOPPINGS_ID,FREE_TOPPINGS_ID,
        //     SPECIAL_INSTRUCTION_ID,SIZE_ID);
        $Categories = TableRegistry::get('Categories');
        $categories = $Categories->find("all")
                ->where(['parent_id' => PIZZA_ID]);
        $categoryArr = array();
        foreach ($categories as $key => $value) {
            $categoryArr[] = $value['id'];
        }
        $category_id = $product['category_id'];

        if (in_array($category_id, $categoryArr)) {

            $pizza_addons = 1;
        } else {
            $pizza_addons = 0;
        }
        $this->set('pizza_addons', $pizza_addons);



        $addon_dips = $AddonCategories->find()
                ->where(['AddonCategories.id' => DIPS_ID])
                ->first();

        $addon_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => TOPPINGS_ID])
                ->first();

        $addon_free_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => FREE_TOPPINGS_ID])
                ->first();

        $addon_special = $AddonCategories->find()
                ->where(['AddonCategories.id' => SPECIAL_INSTRUCTION_ID])
                ->first();

        $size = $AddonCategories->find()
                ->where(['AddonCategories.id' => SIZE_ID])
                ->first();

        $Prices = TableRegistry::get('Prices');
        $pricz = $Prices->find()
                ->where(['Prices.product_id' => $id])
                ->all();
        $this->set('pricz', $pricz);

        $this->set('size', $size);
        $this->set('addon_dips', $addon_dips);

        $this->set('addon_toppings', $addon_toppings);

        $this->set('addon_free_toppings', $addon_free_toppings);

        $this->set('addon_special', $addon_special);

        $Sizes = TableRegistry::get('Sizes');
        $size_price = $Sizes->find()
                ->where(['Sizes.product_id' => $id])
                ->all();
        $this->set('size_price', $size_price);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add() {
        if ($this->check_admin_login() != 1) {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
        $this->layout = 'admin';
        $product = $this->Products->newEntity();
        if ($this->request->is('post')) {

            if ($this->request->data['main_category_id'] == PIZZA_ID) {
                //echo "string"; exit();
                $this->request->data['customize'] = 1;
                $this->request->data['large_price'] = "";

                $default_dips = implode(",", $this->request->data['default_dips']);
                $this->request->data['default_dips'] = $default_dips;
                $default_special = implode(",", $this->request->data['default_special']);
                $this->request->data['default_special'] = $default_special;
                $default_toppings = implode(",", $this->request->data['default_toppings']);
                $this->request->data['default_toppings'] = $default_toppings;

//                   array_push($this->request->data['pdefault'],$this->request->data['psize']);
//                   $default_addons=implode(",",$this->request->data['pdefault']);
//                   $this->request->data['default_addons']=$default_addons;
            } else {
                $default_addons = implode(",", $_POST['default']);
                $this->request->data['default_addons'] = $default_addons;
            }
            if (!isset($this->request->data['category_id'])) {
                //echo "string"; exit();
                $this->request->data['category_id'] = $this->request->data['main_category_id'];
            }

             

            if (isset($_FILES)) {
                $directory = WWW_ROOT . 'products';
                $fileObject = $_FILES['image'];
                $filetype = $fileObject['type'];
                $filesize = $fileObject['size'];
                $filename = $fileObject['name'];
                $filetmpname = $fileObject['tmp_name'];
                move_uploaded_file($filetmpname, $directory . '/' . $filename);
                $this->request->data['image'] = $filename;
            }


            $product = $this->Products->patchEntity($product, $this->request->data);
            if ($pdt = $this->Products->save($product)) {
                $product_id = $pdt->id;

                $ProductAddons = TableRegistry::get('ProductAddons');
                if (($this->request->data['check'] == 1) & (isset($this->request->data['addon']))) {
                    foreach ($this->request->data['addon'] as $key => $value) {
                        $addon = $ProductAddons->newEntity();
                        $addons = array();
                        $addons = array('product_id' => $product_id, 'addon_id' => $value);

                        $addon = $ProductAddons->patchEntity($addon, $addons);
                        $ProductAddons->save($addon);
                        
                        $AddonsPrice = TableRegistry::get('AddonPrices');
                        
                        if(isset($this->request->data['addon_price'][$value]['pickup']) && $this->request->data['addon_price'][$value]['pickup']!='')
                        { 
                    
                             $addon = $AddonsPrice->newEntity();
                             $addons = array();
                             $addons = array('product_id' => $product_id, 'addon_id' => $value,'price'=>$this->request->data['addon_price'][$value]['pickup'],'type'=>'pickup');

                             $addon = $AddonsPrice->patchEntity($addon, $addons);
                             $AddonsPrice->save($addon);
                        }
                        
                        if(isset($this->request->data['addon_price'][$value]['delivery']) && $this->request->data['addon_price'][$value]['delivery']!='')
                        {
                             $addon = $AddonsPrice->newEntity();
                             $addons = array();
                             $addons = array('product_id' => $product_id, 'addon_id' => $value,'price'=>$this->request->data['addon_price'][$value]['delivery'],'type'=>'delivery');

                             $addon = $AddonsPrice->patchEntity($addon, $addons);
                             $AddonsPrice->save($addon);
                        }
                        
                    }
                    
                    
                    /*** for checkig free addons  ***/
                     $FreeAddons = TableRegistry::get('FreeAddons');
                     if(isset($this->request->data['free_addon'])  && $this->request->data['free_addon']!='')
                     {
                         foreach($this->request->data['free_addon'] as $free)
                         {
                            if(isset($this->request->data['free_no_'.$free]) &&  $this->request->data['free_no_'.$free]!='' )
                            {
                                $free_no  =     $this->request->data['free_no_'.$free];
                                
                                $addon = $FreeAddons->newEntity();
                                $addons = array();
                                $addons = array('product_id' => $product_id, 'addon_category_id' => $free,'free_addons' =>$free_no);

                                $addon = $FreeAddons->patchEntity($addon, $addons);
                                $FreeAddons->save($addon);
                            }
                            
                         }
                     }
                }


                $this->Flash->success(__('The product has been saved.'));
               // return $this->redirect("http://mrmozzarella.annarbour.com/products/index");
                //redirect("http://mrmozzarella.annarbour.com/products/index");
                //return $this->redirect(['controller' => 'Categories','action' => 'index']);
                $result= array('id' =>$product->id);
                echo json_encode($result);exit;
            } else {
                $this->Flash->error(__('The product could not be saved. Please, try again.'));
                echo"error";exit;
            }
        }
        $Categories = TableRegistry::get('Categories');
        $Parent_categories = $Categories->find("all")
                ->where(['parent_id' => 0]);
        $this->set('Parent_categories', $Parent_categories);

        $categories1 = $Categories->find("all");

        $this->set('categories1', $categories1);
        $categories = $this->Products->Categories->find('list', ['limit' => 200]);

        $this->set(compact('product', 'categories'));
        $this->set('_serialize', ['product']);

        $AddonCategories = TableRegistry::get('AddonCategories');

        $addonCategories = $AddonCategories->find("all")
                ->where(['parent_id' => 0,'type'=>'other']);
                
        $addonChildCategories = $AddonCategories->find("all")
                ->where(['parent_id <>' => 0,'type'=>'other']);     
                
        $child_arr = array();
        foreach($addonChildCategories as $child)
        {
             $child_arr[$child->parent_id][] = $child->id;
        }
        $this->set('child_arr', $child_arr);
        
        $cat_list = $AddonCategories->find("list")->toArray();
        $this->set('cat_list', $cat_list);
        
                
        $addonCategories1 = $AddonCategories->find("all");
        $this->set('addonCategories', $addonCategories);
        $this->set('addonCategories1', $addonCategories1);
        
        $Addons = TableRegistry::get('Addons');
        $addons = $Addons->find("all")
                ->order(['Addons.order' => asc]);
        $this->set('addons', $addons);

        $addon_dips = $AddonCategories->find()
                ->where(['AddonCategories.id' => DIPS_ID])
                ->first();

        $addon_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => TOPPINGS_ID])
                ->first();

        $addon_free_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => FREE_TOPPINGS_ID])
                ->first();

        $addon_special = $AddonCategories->find()
                ->where(['AddonCategories.id' => SPECIAL_INSTRUCTION_ID])
                ->first();

        $size = $AddonCategories->find()
                ->where(['AddonCategories.id' => SIZE_ID])
                ->first();
                
        $Restaurants= TableRegistry::get('Restaurants');    
        $restaurants_list = $Restaurants->find("list")->toArray();
        
        $directCategories = $AddonCategories->find("list")->where(['direct_pricing ' => 1])->toArray(); 


        $this->set('size', $size);
        $this->set('addon_dips', $addon_dips);
        $this->set('addon_toppings', $addon_toppings);
        $this->set('addon_free_toppings', $addon_free_toppings);
        $this->set('addon_special', $addon_special);
        $this->set('restaurants_list', $restaurants_list);
        $this->set('directCategories', $directCategories);
    }

    /**
     * Edit method
     *
     * @param string|null $id Product id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null) {
        if ($this->check_admin_login() != 1) {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
        $this->layout = 'admin';
        $product = $this->Products->get($id, [
            'contain' => []
        ]);

        if (isset($product['image'])) {
            $image = $product['image'];
        }
        //echo($image); exit();
        if ($this->request->is(['patch', 'post', 'put'])) {

            //echo '<pre>'; print_r($this->request->data);exit();


            if (!isset($this->request->data['category_id'])) {
                //echo "string"; exit();
                $this->request->data['category_id'] = $this->request->data['main_category_id'];
            }
            if ($this->request->data['main_category_id'] == PIZZA_ID) {
                //echo "string"; exit();
                $this->request->data['customize'] = 1;
                $this->request->data['large_price'] = "";
//                array_push($this->request->data['pdefault'],$this->request->data['psize']);
//                $default_addons=implode(",",$this->request->data['pdefault']);
//                $this->request->data['default_addons']=$default_addons;

                $default_dips = implode(",", $this->request->data['default_dips']);
                $this->request->data['default_dips'] = $default_dips;
                $default_special = implode(",", $this->request->data['default_special']);
                $this->request->data['default_special'] = $default_special;
                $default_toppings = implode(",", $this->request->data['default_toppings']);
                $this->request->data['default_toppings'] = $default_toppings; 

            } else {
                $default_addons = implode(",", $_POST['default']);
                $this->request->data['default_addons'] = $default_addons;
            }


            //echo '<pre>'; print_r($this->request->data);exit();

            if (isset($_FILES) && ($_FILES['image']['name'] != "")) {
                //print_r($_FILES);exit();
                //echo "ys";
                $directory = WWW_ROOT . 'products';
                $fileObject = $_FILES['image'];
                $filetype = $fileObject['type'];
                $filesize = $fileObject['size'];
                $filename = $fileObject['name'];
                $filetmpname = $fileObject['tmp_name'];
                move_uploaded_file($filetmpname, $directory . '/' . $filename);
                $this->request->data['image'] = $filename;
            } else {
                //echo "no"; exit();
                $this->request->data['image'] = $image;
            }
            $product = $this->Products->patchEntity($product, $this->request->data);
            if ($this->Products->save($product)) {

                $ProductAddons = TableRegistry::get('ProductAddons');
                $ProductAddons->deleteAll(['ProductAddons.product_id' => $id]);
                if (($this->request->data['check'] == 1) & (isset($this->request->data['addon']))) {

                    foreach ($this->request->data['addon'] as $key => $value) {
                        $addon = $ProductAddons->newEntity();
                        $addons = array();
                        $addons = array('product_id' => $id, 'addon_id' => $value);

                        $addon = $ProductAddons->patchEntity($addon, $addons);
                        $ProductAddons->save($addon);
						
						
						$AddonsPrice = TableRegistry::get('AddonPrices');
						
						$AddonsPrice ->deleteAll(['product_id' => $id, 'addon_id' => $value]);
						
						if(isset($this->request->data['addon_price'][$value]['pickup']) && $this->request->data['addon_price'][$value]['pickup']!='')
						{ 
					
							 $addon = $AddonsPrice->newEntity();
							 $addons = array();
							 $addons = array('product_id' => $id, 'addon_id' => $value,'price'=>$this->request->data['addon_price'][$value]['pickup'],'type'=>'pickup');

							 $addon = $AddonsPrice->patchEntity($addon, $addons);
							 $AddonsPrice->save($addon);
						}
						
						if(isset($this->request->data['addon_price'][$value]['delivery']) && $this->request->data['addon_price'][$value]['delivery']!='')
						{
							 $addon = $AddonsPrice->newEntity();
							 $addons = array();
							 $addons = array('product_id' => $id, 'addon_id' => $value,'price'=>$this->request->data['addon_price'][$value]['delivery'],'type'=>'delivery');

							 $addon = $AddonsPrice->patchEntity($addon, $addons);
							 $AddonsPrice->save($addon);
						}
						
					  if(isset($this->request->data['addon_order']) && !empty($this->request->data['addon_order']))
						{
							 $AddonOrder = TableRegistry::get('AddonOrders');
							 $order_cnt  = 1;
							 
							  $AddonOrder ->deleteAll(['product_id' => $id]);
							 
							 foreach($this->request->data['addon_order'] as $add_order)
							 {
								 $addon_order = $AddonOrder->newEntity();
								 $addons = array();
								 $addons = array('product_id' => $id, 'addon_category_id' => $add_order,'orders'=>$order_cnt);
								 $addon_order  = $AddonOrder->patchEntity($addon_order, $addons);
								 $AddonOrder->save($addon_order);
								 
								 $order_cnt++;
								 
							 }
							 
						}
                    }
					
					
					
					/*** for checkig free addons  ***/
					 $FreeAddons = TableRegistry::get('FreeAddons');
					 $FreeAddons ->deleteAll(['product_id' => $id]);
					 if(isset($this->request->data['free_addon'])  && $this->request->data['free_addon']!='')
					 {
						 foreach($this->request->data['free_addon'] as $free)
						 {
							if(isset($this->request->data['free_no_'.$free]) &&  $this->request->data['free_no_'.$free]!='' )
							{
								$free_no  =     $this->request->data['free_no_'.$free];
								
								$addon = $FreeAddons->newEntity();
								$addons = array();
								$addons = array('product_id' => $id, 'addon_category_id' => $free,'free_addons' =>$free_no);

								$addon = $FreeAddons->patchEntity($addon, $addons);
								$FreeAddons->save($addon);
							}
							
						 }
					 }
                }


                $this->Flash->success(__('The product has been saved.'));
                //return $this->redirect(['controller'=>'products','action' => 'index']);
            } else {
                $this->Flash->error(__('The product could not be saved. Please, try again.'));
            }
        }

        $Categories = TableRegistry::get('Categories');
        $Parent_categories = $Categories->find("all")
                ->where(['parent_id' => 0]);
        $this->set('Parent_categories', $Parent_categories);

        $categories1 = $Categories->find("all");

        $this->set('categories1', $categories1);
        $categories = $this->Products->Categories->find('list', ['limit' => 200]);
        $this->set(compact('product', 'categories'));
        $this->set('_serialize', ['product']);
        
        $AddonCategories = TableRegistry::get('AddonCategories');
        
        $AddonOrders     = TableRegistry::get('AddonOrders');
        $add_order       = $AddonOrders->find()->where(['product_id'=>$id])->count();
        
          $pro_id   = $this->Products->find()->where(['id' => $id ])->first();  
          
          $pro_main = $pro_id->main_category_id;
          
          if($pro_main == PIZZA_ID)
          {
            $a = array(16,17,18);
            $addonCategories =$AddonCategories->find("all")
              ->where(['type'=>'pizza','id NOT IN' => $a]);

              $Sizes= TableRegistry::get('Sizes');
              $size_price = $Sizes->find()
              ->where(['Sizes.product_id' =>$id ])
              ->all();
              foreach ($size_price as $key => $sp) {
                  $size_priceArr[$sp['size']][$sp['type']]["price"]=$sp['price'];
                  $size_priceArr[$sp['size']][$sp['type']]["id"]=$sp['id'];
              }
              $this->set('size_priceArr', $size_priceArr);

              $pizzaSizes = TableRegistry::get('PizzaSizes');	
              $pzSize     = $pizzaSizes->find()->all();
              $this->set('pzSize', $pzSize);

          }
          else if($pro_main == DRINK_ID)
          {
            $addonCategories = $AddonCategories->find("all")
              ->where(['parent_id' => 0,'type'=>'pop']);
          }
          else if($pro_main == DIPPINGSAUCECATEGORY_ID)
          {
            $addonCategories = $AddonCategories->find("all")
              ->where(['parent_id' => 0,'type'=>'dip']);
          }
          else
          {
            $addonCategories = $AddonCategories->find("all")
              ->where(['parent_id' => 0,'type'=>'other']);
          }
        

       // $addonCategories = $AddonCategories->find("all")
         //       ->where(['parent_id' => 0,'type'=>'other']);
        
    /***** for drinks *********/

        $options = array('drink');
        $Combos = TableRegistry::get('Combos');
        $combo_products = $Combos->find()
                ->where(['Combos.type IN' => $options])
                ->all();
        $this->set('combo_products', $combo_products);
        $this->set('_serialize', ['combo']);
        $Addons = TableRegistry::get('Addons');
        $oaddons = $Addons->find("all")
                ->where(['Addons.addon_category_id' => DRINKS_SIZE]);
        foreach ($oaddons as $key => $value) {
            $sizeaddonsArr[$value['id']] = $value['name'];
        }
        $this->set('sizeaddonsArr', $sizeaddonsArr);

        $oaddons = $Addons->find("all")
                ->where(['Addons.addon_category_id' => DRINKS_FLAVOUR]);
        foreach ($oaddons as $key => $value) {
            $flavouraddonsArr[$value['id']] = $value['name'];
        }
        $this->set('flavouraddonsArr', $flavouraddonsArr);


/********for drinks ends*********/
        	
		if($pro_main == PIZZA_ID)
          {
            $addonChildCategories = $AddonCategories->find("all")
                ->where(['parent_id' =>9 ]); 
          }
          else{
            $addonChildCategories = $AddonCategories->find("all")
                ->where(['parent_id <>' => 0,'type'=>'other']); 
          }     
            	
				
		$child_arr = array();
		foreach($addonChildCategories as $child)
		{
			 $child_arr[$child->parent_id][] = $child->id;
		}
		$this->set('child_arr', $child_arr);
		
		$cat_list = $AddonCategories->find("list")->toArray();
		$this->set('cat_list', $cat_list);
		
        $this->set('addonCategories', $addonCategories);
        $addonCategories1 = $AddonCategories->find("all");
        $this->set('addonCategories1', $addonCategories1);
        $Addons = TableRegistry::get('Addons');
        $addons = $Addons->find("all")
                ->order(['Addons.order' => asc]);
        $this->set('addons', $addons);



        $ProductAddons = TableRegistry::get('ProductAddons');
        $productAddons = $ProductAddons->find("all")
                ->where(['product_id' => $id]);
        $this->set('productAddons', $productAddons);
        $this->set('product_id', $id);

        $addon_dips = $AddonCategories->find()
                ->where(['AddonCategories.id' => DIPS_ID])
                ->first();

        $addon_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => TOPPINGS_ID])
                ->first();

        $addon_free_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => FREE_TOPPINGS_ID])
                ->first();

        $addon_special = $AddonCategories->find()
                ->where(['AddonCategories.id' => SPECIAL_INSTRUCTION_ID])
                ->first();

        $size = $AddonCategories->find()
                ->where(['AddonCategories.id' => SIZE_ID])
                ->first();
				
		$Restaurants= TableRegistry::get('Restaurants');	
		$restaurants_list = $Restaurants->find("list")->toArray();
		
		$AddonPrices = TableRegistry::get('AddonPrices');
        $addonPrices = $AddonPrices->find("all")->where(['product_id' => $id]);
		
		$addon_price_arr = array();
		foreach($addonPrices as $ap)
		{
			$addon_price_arr[$ap->addon_id][$ap->type] = $ap->price;
		}
        $this->set('addon_price_arr', $addon_price_arr);
		
		
		$FreeAddons = TableRegistry::get('FreeAddons');
        $freeAddons = $FreeAddons->find("all")->where(['product_id' => $id]);
		
		$free_addon_arr  = array();
		$free_addon_list = array();
		
		foreach($freeAddons as $fa)
		{
			$free_addon_arr[$fa->addon_category_id] = $fa->free_addons;
			$free_addon_list[] = $fa->addon_category_id;
		}
		
		$directCategories = $AddonCategories->find("list")->where(['direct_pricing ' => 1])->toArray();	
		
		$addon_orders       =  $this->get_addon_order($id);
		 $this->set('addon_orders', $addon_orders);
		
        $this->set('free_addon_arr', $free_addon_arr);
		 $this->set('free_addon_list', $free_addon_list);
		
				
        $this->set('size', $size);
        $this->set('addon_dips', $addon_dips);
        $this->set('addon_toppings', $addon_toppings);
        $this->set('addon_free_toppings', $addon_free_toppings);
        $this->set('addon_special', $addon_special);
		 $this->set('restaurants_list', $restaurants_list);
		 $this->set('directCategories', $directCategories);
    }

    /**
     * Delete method
     *
     * @param string|null $id Product id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null) {
        if ($this->check_admin_login() != 1) {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
        $this->request->allowMethod(['post', 'delete']);
        $product = $this->Products->get($id);
        if ($this->Products->delete($product)) {
            $this->Flash->success(__('The product has been deleted.'));
            echo"success";exit;
        } else {
            $this->Flash->error(__('The product could not be deleted. Please, try again.'));
            echo"error";exit;
        }
        return $this->redirect(['action' => 'index']);
    }

    public function menu() {
        //$this->layout = 'admin';
        $this->paginate = [
            'contain' => ['Categories']
        ];


     $delLoc  = 0;
     $delType = '';
		
	 $delType = $this->request->session()->read('Config.deltype');
     $delLoc  = $this->request->session()->read('Config.location');
     

     $cat_id = "";
	 
	 $excluded_menu = '0';
	 if($delType=='pickup')
	 {
		 $excluded_menu  = DELIVERY_SPECIAL_ID;
	 }
	 else
	 {
		 //$excluded_menu  =  ONLINE_SPECIAL_ID;
	 }




	 $this->Products = TableRegistry::get('Products');

        $this->set('products', $this->paginate($this->Products));
        $this->set('_serialize', ['products']);
        $Categories = TableRegistry::get('Categories');

        if($delLoc == OTTAWA_ID)
        {
        
                $categories = $Categories->find("all")
                ->where(['parent_id ' => 0 , 'status ' => 1 , 'id <>'=>  $excluded_menu])->order(['Categories.ordering'=>'asc']);
        }
        else
        {

            $cat_id = unserialize(MENU_ID);
            $categories = $Categories->find("all")
                ->where(['parent_id ' => 0 , 'status ' => 1 , 'id NOT IN'=>  $cat_id])->order(['Categories.ordering'=>'asc']);
        }
     
        
//        $cat=array(3,4,41,43,52);
//        $categories = $Categories->find("all")
//            ->where(['id IN' =>$cat]);

        $this->set('categories', $categories);
        // $pizza = $Categories->find("all")
        //    ->where(['Categories.id ' =>PIZZA_ID]);
        // $this->set('pizza', $pizza);
        //echo SALAD_ID;exit;
        $Products = TableRegistry::get('Products');
        $salad = $Products->find("all")
                ->where(['Products.main_category_id' => SALAD_ID,'restaurants_id'=>$delLoc]);
        foreach ($salad as $key => $value) {
            $sal = $value['id'];
        }
        // echo $sal;
        //print_r($salad);
        // exit;
        $this->set('salad_id', $sal);
    }

    public function products_list() {
        //$this->layout = 'admin';
        $this->paginate = [
            'contain' => ['Categories']
        ];
        $this->set('products', $this->paginate($this->Products));
        $this->set('_serialize', ['products']);
    }

    public function products_details($id = null) {
        
		/** for fimnind location and type **/
		
		$delLoc  = 0;
		$delType = '';
		
		$delType = $this->request->session()->read('Config.deltype');
		$delLoc  = $this->request->session()->read('Config.location');
		
		
		
        $Sizes = TableRegistry::get('Sizes');
        $pizza_size_prices = $Sizes->find()
                ->where(['Sizes.product_id' => $id,'Sizes.type'=>$delType])
                ->all();
	
        $product = $this->Products->get($id, ['contain' => ['Categories', 'Addons']]);

		$PizzaSizes = TableRegistry::get('PizzaSizes');
		$ps         = $PizzaSizes->find()->all();
		foreach($ps as $sz)
		{
			$ps_arr[$sz->size_value] = $sz->size_label;
		}
		
		
        $AddonCategories   = TableRegistry::get('AddonCategories');
        $Prices 		   = TableRegistry::get('Prices');
        $addon_type_prices = $Prices->find("all")->where(['Prices.product_id' => id])->all();

        $Addons = TableRegistry::get('Addons');
        $addonCategories = $AddonCategories->find("all")->order(['AddonCategories.order' => asc]);
        $addon_maincat = $AddonCategories->find()->where(['AddonCategories.parent_id' => 0])->all();

        $query = $Addons->find()->order(['Addons.order' => 'asc'])->all();
		

        // echo '<pre>';print_r($addon_type_prices); exit();

        $addon_dips = $AddonCategories->find()
                ->where(['AddonCategories.id' => DIPS_ID])
                ->first();

        $addon_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => TOPPINGS_ID])
                ->first();

        $addon_free_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => FREE_TOPPINGS_ID])
                ->first();

        $addon_special = $AddonCategories->find()
                ->where(['AddonCategories.id' => SPECIAL_INSTRUCTION_ID])
                ->first();

      

        $this->set('addon_dips', $addon_dips);
        $this->set('addon_toppings', $addon_toppings);
        $this->set('addon_free_toppings', $addon_free_toppings);
        $this->set('addon_special', $addon_special);
        // $this->set('size', $size);



        $this->set('addon_type_prices', $addon_type_prices);
		$this->set('ps_arr', $ps_arr);
        $this->set('addon_maincat', $addon_maincat);
        $this->set('addonCategories', $addonCategories);
        $this->set('addons', $query);
        $this->set('product', $product);
		$this->set('pizza_size_prices', $pizza_size_prices);
        $this->set('_serialize', ['product']);

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['pizza'][$key];
            $this->set('selected_items', $selected_items);
            $this->set('session_key', $key);


        }
    }

    public function more_details($id = null) {
        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => $id])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');


        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->order(['Addons.order' => asc])
                ->all();

        $this->set('addons_all', $addons_all);


        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $selection = $AddonCategories->find()
                ->where(['AddonCategories.selection' => "single"])
                ->all();
        $this->set('selection', $selection);


        $addons = $Addons->find()
                ->order(['Addons.order' => asc])
                ->all();
        $this->set('addons', $addons);
        $this->set('addon_catArr', $addon_catArr);
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Addons']
        ]);

        // echo $product['category_id'];exit;

        if ((BASKET_ID == $product['category_id'])) {
            $addons_side = $Addons->find()
                    ->where(['Addons.addon_category_id ' => BSIDE_ID])
                    ->all();
            $this->set('addons_side', $addons_side);
        }


        $this->set('product', $product);
        $this->set('_serialize', ['product']);

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['custom'][$key];
            //echo "<pre>";print_r($selected_items); exit;
            $this->set('selected_items', $selected_items);
            $this->set('session_key', $key);

//        unset($cart_items['pizza'][$key]);
//        $session->delete('Cartarray1');
//        $session = new Session();
//        $sessionData = $session->write('Cartarray1',$cart_items);
        }
    }
    
    
    
    
    /* added by anvis.start */
    
    
        public function salad_extra_details($id = null) {
        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => $id])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');
        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->all();
        $this->set('addons_all', $addons_all);


        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $selection = $AddonCategories->find()
                ->where(['AddonCategories.selection' => "single"])
                ->all();
        $this->set('selection', $selection);


        $addons = $Addons->find("all");
        $this->set('addons', $addons);
        $this->set('addon_catArr', $addon_catArr);
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Addons']
        ]);



        $this->set('product', $product);
        $this->set('_serialize', ['product']);

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['custom'][$key];
            //echo "<pre>";print_r($selected_items); exit;
            $this->set('selected_items', $selected_items);
            $this->set('session_key', $key);

//        unset($cart_items['pizza'][$key]);
//        $session->delete('Cartarray1');
//        $session = new Session();
//        $sessionData = $session->write('Cartarray1',$cart_items);
        }
    }
    
    /* added by anvis.end */

    public function extra_details($id = null) {
        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => $id])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');
        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->all();
        $this->set('addons_all', $addons_all);


        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $selection = $AddonCategories->find()
                ->where(['AddonCategories.selection' => "single"])
                ->all();
        $this->set('selection', $selection);


        $addons = $Addons->find("all");
        $this->set('addons', $addons);
        $this->set('addon_catArr', $addon_catArr);
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Addons']
        ]);



        $this->set('product', $product);
        $this->set('_serialize', ['product']);

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['custom'][$key];
            //echo "<pre>";print_r($selected_items); exit;
            $this->set('selected_items', $selected_items);
            $this->set('session_key', $key);

//        unset($cart_items['pizza'][$key]);
//        $session->delete('Cartarray1');
//        $session = new Session();
//        $sessionData = $session->write('Cartarray1',$cart_items);
        }
    }

    public function size_calculate() {
        $product_id = $_POST['product_id'];
        $size = $_POST['size'];
		
		$delLoc  = 0;
		$delType = '';
		$delType = $this->request->session()->read('Config.deltype');
		$delLoc  = $this->request->session()->read('Config.location');
		
		
        $Sizes = TableRegistry::get('Sizes');
        $addon_spl_price_details = $Sizes->find()
                ->where(['Sizes.product_id' => $product_id, 'Sizes.size' => $size,'Sizes.type'=>$delType])
                ->first();
        if (isset($addon_spl_price_details['price'])) {
            $price = $addon_spl_price_details['price'];
            echo $price;
            exit;
        }
        exit;
    }

    public function calculate() {
        // echo  $_POST['size'];exit;
        //print_r($_POST);exit();
        $product_count = $_POST['product_count'];
        $product_id = $_POST['product_id'];
        $isCombo       = false;

       

        $total = $_POST['start_price'];
        $size = $_POST['size'];
        $Prices = TableRegistry::get('Prices');

        $combo4largeFlag = false;
        $combo4largeID   = '';
        if(isset($_POST['product_combo_category']) && $_POST['product_combo_category'] =='party'):
           $combo4largeFlag = true;
        endif;
         if(isset($_POST['product_combo_id']) && $_POST['product_combo_id'] !=''):
           $combo4largeID = $_POST['product_combo_id'];
           $isCombo       = true;
        endif;
     

//print_r($_POST['addon']['43']['145']);exit;
        $ic = 1;
        foreach ($_POST['addon'] as $key => $main) {
            //print_r($main);

            foreach ($main as $key1 => $subcat) {

                
                  if($combo4largeFlag==true && $combo4largeID== FOUR_LARGE_PIZZA_ID):
                        $subcat['default'] = 0;
                  endif;
                if ($subcat['default'] == 0) {



                    //echo $key1."=".$value1." ,";
                    //print_r($subcat); 
                    //echo $subcat['side'];
                    $side = $subcat['side'];
                    if ($side == "left" || $side == "right") {
                        $side1 = "full";
                    } else {
                        $side1 = "full";
                    }

                    $type = $subcat['type'];


                    $addon_id = $subcat['addon_id'];
                    $addon_price_details = $Prices->find()
                            ->where(['Prices.product_id' => $product_id, 'Prices.side' => $side1, 'Prices.type' => $type, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                            ->first();
                    if (isset($addon_price_details['price'])) {
                        $addon_price = $addon_price_details['price']; 
            
             /** for addons with half size.. */

             if($side1 == 'half'):
                $addon_price = $addon_price_details['price']/2;
                $addon_price = number_format((float)$addon_price , 2, '.', '');  
             endif;

            /** for checking max combo count  ***/
             if($side1 == 'full' && $isCombo==true ):
                $maxCount = $_POST['max_sauce_count'];
                $sauCount = $_POST['sauce_count'];
                $max_sau  = $sauCount - $maxCount;
                if($max_sau=='0.5'):
                	$addon_price = $addon_price_details['price']/2;
                	$addon_price = number_format((float)$addon_price , 2, '.', '');  
		endif;
             endif;
            /*********************/     
      
                        if($combo4largeFlag==true && $combo4largeID== FOUR_LARGE_PIZZA_ID):
                          if($ic>6): 
                            
                          $total+=$addon_price;
                          endif;
                        else:
                          $total+=$addon_price;
                        endif;
                      $ic++;
                    }
                }
            }
        }

        if (isset($_POST['dips'])) {
            foreach ($_POST['dips'] as $key => $value) {
                
                 /** dips[][addon_id] changd to dips[][addon_id][]  in speciality pizza **/

                  if(isset($_POST['product_combo_id']) && $_POST['product_combo_id'] !=''):
                       $addon_id = $value['addon_id'];
                       $_POST['tagcatid']=SAUCEID;
                  else:
                       $addon_id = $value['addon_id'][0]; 
                  endif;
                 

                $addon_dip_price_details = $Prices->find()
                        ->where(['Prices.product_id' => $product_id, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                        ->first();
                if (isset($addon_dip_price_details['price'])) {
                    $addon_dip_price = $addon_dip_price_details['price'];
                    //echo $addon_price; exit();
                    $total+=$addon_dip_price;
                }
          /**only for dough/sauce **/
           
                //$tgArray = array(350,351,369,370,371,372,373,443); 
                if(isset($_POST['tagcatid']) && $_POST['tagcatid']==54): 
                    if(in_array($addon_id,$tgArray )): 
                       

		                if($size=='small'):
		                     $Dprice = 0.50;
		                elseif($size=='medium'):
		                     $Dprice = 1.00;
		                elseif($size=='large'):
		                     $Dprice = 1.50;
		                else:
		                     $Dprice = 2.00;
		                endif;

                       

                        //$total += $Dprice;
                        $total = $total + $Dprice;
                    endif;
                endif;
            }
           
        }

        if (isset($_POST['special']['addon_id'])) {

            $addon_id = $_POST['special']['addon_id'];
            $addon_spl_price_details = $Prices->find()
                    ->where(['Prices.product_id' => $product_id, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                    ->first();
            if (isset($addon_spl_price_details['price'])) {
                $addon_spl_price = $addon_spl_price_details['price'];
                //echo $addon_price; exit();


 
                $total+=$addon_spl_price;
            }
        }

        if (isset($_POST['crust'])) {
            $price = 4;
            $crust = $_POST['crust'];
            if ($crust == "gluten") {
                $total+=$price;
            }
        }
//        if(isset($_POST['size']))
//         {
//             $size=$_POST['size'];
//             if($size!="small")
//             {
//
//
//                 $Sizes = TableRegistry::get('Sizes');
//                 $addon_spl_price_details = $Sizes->find()
//                     ->where(['Sizes.product_id' => $product_id, 'Sizes.size' => $size])
//                     ->first();
//                 if (isset($addon_spl_price_details['price']))
//                 {
//                     $addon_spl_price = $addon_spl_price_details['price'];
//                     //echo $addon_price; exit();
//                     $total += $addon_spl_price;
//                 }
//
//             }
//
//        }
        //$size=$_POST['size'];

        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        echo($final);
        exit();

        //print_r($_POST); exit();
    }

    public function total_price() {
        
        $maincategory_id = $_POST['maincategory_id'];
        $delLoc  = $this->request->session()->read('Config.location');
        $product_count = $_POST['product_count'];
        $product_id = $_POST['product_id'];
       
        $wings_type = "";


        $sizeCount      = 1;
        $sizeCounttotal = 0;
        $totalFlag      = True;



        if (isset($_POST['wings_type'])) {
            $wings_type = $_POST['wings_type'];
        }

        if (isset($_POST['salad_size'])) {
            $salad_size = $_POST['salad_size'];
        } else {
            $salad_size = "";
        }

        if (isset($_POST['psize'])) {
            $psize = $_POST['psize'];
        } else {
            $psize = "";
        }
        $total = $_POST['product_baseprice'];
        if (($maincategory_id == APPETIZER_ID) || ($maincategory_id == POUTINE_ID) || ($maincategory_id == WING_ID) || ($maincategory_id == SALAD_ID)) {
            if (($wings_type == 'breaded') || ($salad_size == 'large') || ($psize == 'large')) {
                $total = $_POST['product_largeprice'];
            } else {
                $total = $_POST['product_baseprice'];
            }
        }

        if ($maincategory_id == SALAD_ID) {
            if (isset($_POST['salad_size'])) {
                $green_price = "";
                $salad_size = $_POST['salad_size'];
                $salad_green_id = $_POST['salad_green_id'];
                $green_price = $_POST['salad_green'][$salad_green_id][$salad_size];
                $total+=$green_price;
            }
        }
        if ($product_id == ITALIAN_ALFREDO_ID) {
            $italian_alfredo_type = $_POST['italian_alfredo_type'];
            $italian_alfredo_type_price = $_POST['italian_alfredo_type_price_' . $italian_alfredo_type];
            $total = $italian_alfredo_type_price;
        }

        if ($product_id == ITALIAN_SPAGHETTI_ID) {
            $italian_spaghetti_type = $_POST['italian_spaghetti_type'];
            $italian_spaghetti_type_price = $_POST['italian_spaghetti_type_price_' . $italian_spaghetti_type];
            $total = $italian_spaghetti_type_price;
        }

        if ($maincategory_id == DRINK_ID) {
            if (isset($_POST['drinks_size'])) {
                $drinks_size = $_POST['drinks_size'];
                $total = $_POST['drinks_size_' . $drinks_size];
            } else {
                $drinks_size = "";
            }
        }

        if ($maincategory_id == DESSERT_ID) {
            if (isset($_POST['desert_type'])) {
                $desert_type = $_POST['desert_type'];
                foreach ($desert_type as $key=>$value)
                {
                    $desert_type1 = str_replace(' ', '', $value);
                    $total+=$_POST['desert_type_' . $desert_type1];
                }
                
            } else {
                $drinks_size = "";
            }
        }

        if ($product_id == APPETIZER_STRIPS_ID) {

            $apetizer_type = $_POST['apetizer_type'];
            if (in_array('CHEESE', $apetizer_type)) {
                if (in_array('BACON', $apetizer_type)) {
                    $total+=3;
                } else {
                    $total+=2;
                }
            } else if (in_array('BACON', $apetizer_type)) {
                $total+=2;
            }
            //print_r($apetizer_type);exit;
        }


         $wingflag = false;
         if(isset($_POST['product_combo_category']) && ($_POST['product_combo_category']=='party' || $_POST['product_combo_category']=='manager' || $_POST['product_combo_category']=='delivery_spec' || $_POST['product_combo_category']=='online') && $_POST['productType'] =='wings') :
        
         $this->Combo = TableRegistry::get('Combos');
         $product_wing_details = $this->Combo->find()
                ->where(['id' => $_POST['product_combo_id']])
                ->first();
         $max_sauce_count = $product_wing_details->sauce_count;
         
         $wingflag = true;
         endif;  




        $Prices = TableRegistry::get('Prices');
        
         $iWingcnt = 1;
        if (isset($_POST['addons'])) {
            foreach ($_POST['addons'] as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    if (isset($value1['addon_id'])) {

                        if ((($maincategory_id == POUTINE_ID) && ($psize == 'large')) || (($maincategory_id == APPETIZER_ID) && ($psize == 'large'))) {
                            $addon_price = $value1['lprice'];
                        } else {
                            $addon_price = $value1['price'];
                        }
                        if ($value1['addon_id'] == NOSIDE_ID) {
                            $total-=NOSIDE_PRICE;
                        } else {


                            if($wingflag==true && $_POST['productType'] =='wings'): 

                                 /*** for counting sauce after no. of defaults***/

		                    $totsize     = $_POST['drinks_count'][$value1['addon_id']];
			            if($totsize!='' && $totsize!=0):
			               $sizeCount = $totsize;
			            endif;
		                    $sizeCounttotal += $sizeCount;
		                  
		                    if($sizeCounttotal>$max_sauce_count && $totalFlag == true)
		                     {
		                         $sizeCount = $sizeCounttotal-$max_sauce_count;
		                         $totalFlag = false;
		                     }

		                      
		                 /**********************/

                               


                                if($sizeCounttotal > $max_sauce_count):
                                            if($delLoc == OTTAWA_ID)
                                            {
                                                $addon_price = $value1['price'] * $sizeCount;
                                                $addon_price = $addon_price+OTTAWA_DIP_DIFFRENCE;
                                            }
                                            else if($delLoc == MARKHAM_ID)
                                            {
                                                $addon_price = $value1['price'] * $sizeCount;
                                            }
                                            else if($delLoc == DANFORTH_ID)
                                            {
                                                $addon_price = $value1['price'] * $sizeCount;
                                                $addon_price = $addon_price+DANFORTH_DIP_DIFFRENCE;
                                            }
                                            else
                                            {
                                               $addon_price = $value1['price'] * $sizeCount; 
                                            }
                                         
                                	$total+=$addon_price;
                                endif;
                            else:

                                /** for number of sauce count  **/
                                $sizeCount         = 1;
                                $totsize           = $_POST['drinks_count'][$value1['addon_id']];

                                if($totsize!='' && $totsize!=0):
		                    $sizeCount = $totsize;
		                endif;



                                if($maincategory_id == APPETIZER_ID):
                                    $default_Appetizer = unserialize (APPETIZER_SAUCE_FREE);
                                    if(isset($default_Appetizer[$product_id]) && $default_Appetizer[$product_id]):
                                         $maxAppetizer = $default_Appetizer[$product_id];

                                         $sizeCounttotal += $sizeCount;
                                         
                                         if($sizeCounttotal < $maxAppetizer && $totalFlag == true)
				             {
				                 $sizeCount = 0;
				             }
                            
                                         if($sizeCounttotal>=$maxAppetizer && $totalFlag == true)
				             {
				                 $sizeCount = $sizeCounttotal-$maxAppetizer;
				                 $totalFlag = false;
				             }
                                         

                                    endif;
                                endif;

                              /** @csp-May-27 - for removing charging of Proce for sauce in WRAPS **/
 				
                                if($maincategory_id == WRAP_ID):
				       $sizeCount = 0; 
                                endif;
 
                                
                        
                                /*if ($product_id == APPETIZER_STRIPS_ID)
				  {
                                     if($totsize>=1 && $totalFlag == true)
		                     {
		                        $totsize= $sizeCount = $totsize-1;
		                         $totalFlag = false;
		                     }
				  }*/
                        

		                /*if($totsize!='' && $totsize!=0):
		                    $sizeCount = $totsize;
		                endif;*/

                              

                                $addon_price = $addon_price*$sizeCount;
                                $total+=$addon_price;
                            endif;
                            
                        }
                      $iWingcnt++;
                    }
                }
            }
        }






        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        print_r($final);
        exit();

        //print_r($_POST); exit();
    }

    public function price_calculate() {
        //echo $_GET['addon_category_id'];exit();
        // $maincategory_id=$_GET['maincategory_id'];
        // $subcategory_id=$_GET['subcategory_id'];
        // $addon_category_id=$_GET['addon_category_id'];
        //print_r($_POST);exit();
        $total_price = $_POST['inital_price'];
        if (!empty($_POST['addons'])) {
            foreach ($_POST['addons'] as $key => $value) {

                $total_price+=$value['total'];
            }
        }

        if (isset($_POST['product_count']) && $_POST['product_count'] != "") {
            $product_count = $_POST['product_count'];
            $final_price = $total_price * $product_count;
            $final_price = number_format((float) $final_price, 2, '.', '');
            echo $final_price;
            exit;
        }
        $total_price = number_format((float) $total_price, 2, '.', '');

        echo $total_price;
        exit();
    }
/* anvis .star */
     public function salad_products_view() {
        $tab_index = $_POST['tab_index'];
        $this->layout = false;
        $subcatArr = array();
        $productsArr = array();
        $maincategory_id = $_POST['maincategory_id'];
        $Categories = TableRegistry::get('Categories');
        $subcat = $Categories->find()
                ->where(['Categories.parent_id' => $maincategory_id])
                ->order(['Categories.order ' => 'asc'])
                ->all();

        foreach ($subcat as $key => $value) {
            if ($value['id'] != TWINCATEGORY_ID) {
                $subcatArr[$value['id']] = $value['name'];
            }
        }
        //print_r($subcatArr);exit;
        $Products = TableRegistry::get('Products');
        if (empty($subcatArr)) {
            $products = $Products->find("all")
                    ->where(['Products.category_id' => $maincategory_id])
                    ->order(['Products.order' => 'asc'])
                    ->all();
            foreach ($products as $key => $value) {
                $productsArr[] = array('id' => $value['id'], 'name' => $value['name'], 'description' => $value['description'],
                    'base_price' => $value['base_price'], 'image' => $value['image'], 'customize' => $value['customize'], 'large_price' => $value['large_price']);
            }
        } else {


//           	           $products = $Products->find("all")
//                           ->order(['Products.order'=>'asc']);
            $products = $Products->find("all")
                    ->order(['Products.order' => 'asc']);

            foreach ($products as $key => $value) {
                $productsArr[$value['category_id']][] = array('id' => $value['id'], 'name' => $value['name'], 'description' => $value['description'],
                    'base_price' => $value['base_price'], 'image' => $value['image'], 'customize' => $value['customize']);
            }
        }
        //print_r($productsArr);exit;

		
		
		
        $sizes = $Sizes->find("all");
        foreach ($sizes as $key => $value) {
            $sizeArr[$value['product_id']][] = array('id' => $value['id'], 'name' => $value['name'], 'size' => $value['size'], 'price' => $value['price']);
        }
        $this->set('sizeArr', $sizeArr);

        $maincategory_details = $Categories->find()
                ->where(['Categories.id' => $maincategory_id])
                ->first();
        $maincategory_name = $maincategory_details['name'];

        $this->set('maincategory_id', $maincategory_id);

        $this->set('maincategory_name', $maincategory_name);

        $this->set('subcatArr', $subcatArr);
        $this->set('productsArr', $productsArr);
        $this->set('tab_index', $tab_index);
        $ComboCategories = TableRegistry::get('ComboCategories');
        $combocategories = $ComboCategories->find("all")
                ->where(['ComboCategories.id' => PIZZA_COMBO_ID])
                ->all();
        foreach ($combocategories as $key => $value) {
            $pizza_combo_name = $value['name'];
        }
        $this->set('pizza_combo_name', $pizza_combo_name);
        $Combo = TableRegistry::get('Combo');
        $pizza_combo = $Combo->find("all")
                ->where(['Combo.combo_category' => PIZZA_COMBO_ID])
                ->all();
        foreach ($pizza_combo as $key => $value) {
            $pizza_comboArr[] = array('id' => $value['id'], 'name' => $value['name'], 'image' => $value['image'], 'price' => $value['price']);
        }
        $this->set('pizza_comboArr', $pizza_comboArr);
        $ComboProducts = TableRegistry::get('ComboProducts');
        $pizza_combo_products = $ComboProducts->find("all");
        foreach ($pizza_combo_products as $key => $value) {
            $pizza_combo_productsArr[$value['combo_id']][] = array('combo_id' => $value['combo_id']);
        }
        $this->set('pizza_combo_productsArr', $pizza_combo_productsArr);

        $maincategoryIdArr = array(POUTINE_ID,  ITALIAN_ID, FOOTLONG_ID, WRAP_ID,  BURGER_ID,  DESSERT_ID);
        $this->set('maincategoryIdArr', $maincategoryIdArr);
        
         $splmaincategoryIdArr = array( BASKET_ID, WING_ID, FINGERS_ID, SHRIMP_ID);
        $this->set('splmaincategoryIdArr', $splmaincategoryIdArr);


        // twins
        $Combos = TableRegistry::get('Combos');
        $twin_details = $Combos->find()
                ->where(['Combos.type' => 'party'])
                ->order(['Combos.id' => asc])
                ->all();
        foreach ($twin_details as $key => $value) {
                    $twin_detailsArr[]=array(
                        'id'=>$value['id'],
                        'pizza_count'=>$value['pizza_count'],
                        'size'=>$value['size'],
                        'wings'=>$value['wings'],
                        'count'=>$value['count'],
                        'wings_price'=>$value['wings_price'],
                        'price'=>$value['price'],
                        'name'=>$value['name'],
                        'picture'=>$value['image'],
                        );
                }
        $this->set('twin_details', $twin_detailsArr);


        // twins

        $this->render('salad_products_view');
        //print_r($subcatArr);
        // exit();
    }
    
    /* anvis .end */
    public function products_view() {


	/*** for location wise listing the products ***/
        $delLoc  = 0;
	$delType = 'pickup';
	
	$delType = $this->request->session()->read('Config.deltype');
	$cheLoc  = $delLoc  = $this->request->session()->read('Config.location');
       
        
        /****/

         
        $tab_index = $_POST['tab_index'];
        $this->layout = false;
        $subcatArr = array();
        $productsArr = array();

        $category_id = $_POST['category_id'];


        $maincategory_id = $_POST['maincategory_id'];
        $Categories = TableRegistry::get('Categories');
        $subcat = $Categories->find()
                ->where(['Categories.parent_id' => $maincategory_id])
                ->order(['Categories.order ' => 'asc'])
                ->all();

        foreach ($subcat as $key => $value) {
            if ($value['id'] != TWINCATEGORY_ID) {
                $subcatArr[$value['id']] = $value['name'];
            }
        }
        //print_r($subcatArr);exit;
        $Products = TableRegistry::get('Products');
        if (empty($subcatArr)) {

            $location = 0;
           // if($maincategory_id!=PIZZA_ID)
           // {
		$location = $delLoc ;
          //  }
       

            $products = $Products->find("all")
                    ->where(['Products.category_id' => $maincategory_id,'Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN])
                    ->order(['Products.order' => 'asc'])
                    ->all();

        
            foreach ($products as $key => $value) {
                $productsArr[] = array('id' => $value['id'], 'name' => $value['name'], 'description' => $value['description'],
                    'base_price' => $value['base_price'], 'image' => $value['image'], 'customize' => $value['customize'], 'large_price' => $value['large_price']);
            }
        } else {
		$location = 0;
	
			$location = $delLoc ;
		  
           $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN])
                    ->order(['Products.order' => 'asc']);
        
                // if($delLoc == OTTAWA_ID)
                // {
                //    $products =  $this->ottawa_product($delLoc);
                    
                // }
                // else if ($delLoc == DANFORTH_ID)
                // {
                //    $products =   $this->danforth_product($delLoc);
                    
                // }
                // else if($delLoc == MARKHAM_ID)
                // {

                //      $products =   $this->markham_product($delLoc);
                   


                // }
       

//           	           $products = $Products->find("all")
//                           ->order(['Products.order'=>'asc']);



            


            foreach ($products as $key => $value) {
                $productsArr[$value['category_id']][] = array('id' => $value['id'], 'name' => $value['name'], 'description' => $value['description'],
                    'base_price' => $value['base_price'], 'image' => $value['image'], 'customize' => $value['customize'],'day_no'=>$value['day_no']);
            }
		
			if($maincategory_id==PIZZA_ID)
			{
                           
				$makeproducts = $Products->find("all")->where(['Products.restaurants_id'=>$delLoc,'Products.category_id'=> MAKE_YOUR_OWN])->order(['Products.order' => 'asc']);


				foreach ($makeproducts as $key => $value) {
                $productsArr[$value['category_id']][] = array('id' => $value['id'], 'name' => $value['name'], 'description' => $value['description'],
                    'base_price' => $value['base_price'], 'image' => $value['image'], 'customize' => $value['customize'],'day_no'=>$value['day_no']);
            }
			}




        }
        //print_r($productsArr);exit;

		
		
        $Sizes = TableRegistry::get('Sizes');
		
		$ZCount = $Sizes->find()->where(['Sizes.type'=>$delType])->count();
		if($ZCount==0)
		{
			$delLoc  = 0;
		}
       
        $sizes = $Sizes->find()->where(['Sizes.type'=>$delType])->all();
        foreach ($sizes as $key => $value) {
            $sizeArr[$value['product_id']][] = array('id' => $value['id'], 'name' => $value['name'], 'size' => $value['size'], 'price' => $value['price']);
			$pizzabase[$value['product_id']][] = $value->price;
        }
		
		//echo '<pre>';print_r($pizzabase);
        $this->set('sizeArr', $sizeArr);
		$this->set('pizzabase', $pizzabase);
		
        $maincategory_details = $Categories->find()
                ->where(['Categories.id' => $maincategory_id])
                ->first();
        $maincategory_name = $maincategory_details['name'];

        $this->set('maincategory_id', $maincategory_id);

        $this->set('maincategory_name', $maincategory_name);

        $this->set('subcatArr', $subcatArr);
        $this->set('productsArr', $productsArr);
        $this->set('tab_index', $tab_index);
        $ComboCategories = TableRegistry::get('ComboCategories');
        $combocategories = $ComboCategories->find("all")
                ->where(['ComboCategories.id' => PIZZA_COMBO_ID])
                ->all();
        foreach ($combocategories as $key => $value) {
            $pizza_combo_name = $value['name'];
        }
        $this->set('pizza_combo_name', $pizza_combo_name);
        $Combo = TableRegistry::get('Combo');
        $pizza_combo = $Combo->find("all")
                ->where(['Combo.combo_category' => PIZZA_COMBO_ID])
                ->all();
        foreach ($pizza_combo as $key => $value) {
            $pizza_comboArr[] = array('id' => $value['id'], 'name' => $value['name'], 'image' => $value['image'], 'price' => $value['price']);
        }
        $this->set('pizza_comboArr', $pizza_comboArr);
        $ComboProducts = TableRegistry::get('ComboProducts');
        $pizza_combo_products = $ComboProducts->find("all");
        foreach ($pizza_combo_products as $key => $value) {
            $pizza_combo_productsArr[$value['combo_id']][] = array('combo_id' => $value['combo_id']);
        }
        $this->set('pizza_combo_productsArr', $pizza_combo_productsArr);

        $maincategoryIdArr = array(POUTINE_ID,  ITALIAN_ID, FOOTLONG_ID, WRAP_ID,  BURGER_ID,  DESSERT_ID);
        $this->set('maincategoryIdArr', $maincategoryIdArr);
        
         $splmaincategoryIdArr = array( BASKET_ID, WING_ID, FINGERS_ID, SHRIMP_ID);
        $this->set('splmaincategoryIdArr', $splmaincategoryIdArr);

		$PizzaSizes = TableRegistry::get('PizzaSizes');
		$ps         = $PizzaSizes->find()->all();
		foreach($ps as $sz)
		{
			$ps_arr[$sz->size_value] = $sz->size_label;
		}
		$this->set('ps_arr', $ps_arr);

        // twins
		
        $Combos = TableRegistry::get('Combos');
        $twin_details = $Combos->find()
                 ->where(['Combos.type' => 'party','Combos.name'=>'Twin Pizza Special','Combos.restaurent_id'=>$delLoc])
                ->order(['Combos.id' => 'asc'])
                ->all();
        foreach ($twin_details as $key => $value) {
                    $twin_detailsArr[]=array(
                        'id'=>$value['id'],
                        'pizza_count'=>$value['pizza_count'],
                        'size'=>$value['size'],
                        'wings'=>$value['wings'],
                        'count'=>$value['count'],
                        'wings_price'=>$value['wings_price'],
                        'price'=>$value['small_price'],
                        'name'=>$value['name'],
                        'picture'=>$value['image'],
                        );
                }
        $this->set('twin_details', $twin_detailsArr);


        // twins

        $MyLocation  = $this->request->session()->read('Config.location');
        
       
        
    }

    public function ottawa_product($location)
    {

        $today = date("l");
        $Products = TableRegistry::get('Products');
        if($today == "Monday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [681,683,684,685]])
                    ->order(['Products.order' => 'asc']);  
        }
        else if($today == "Tuesday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [682,683,684,685]])
                    ->order(['Products.order' => 'asc']);  
        }
        else if($today == "Wednesday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [681,682,684,685]])
                    ->order(['Products.order' => 'asc']);  
        }
        else if($today == "Thursday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [681,682,683,685]])
                    ->order(['Products.order' => 'asc']);  
        }
        else if($today == "Sunday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [681,682,683,684]])
                    ->order(['Products.order' => 'asc']);  
        }
        else
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [681,682,683,684,685]])
                    ->order(['Products.order' => 'asc']);  
        }

        return $products;
    }

    public function danforth_product($location)
    {
        $today = date("l");
        $Products = TableRegistry::get('Products');
        if($today == "Monday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [706,707,708,709]])
                    ->order(['Products.order' => 'asc']);  
        }
        else if($today == "Tuesday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [705,707,708,709]])
                    ->order(['Products.order' => 'asc']);  
        }
        else if($today == "Wednesday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [706,705,708,709]])
                    ->order(['Products.order' => 'asc']);  
        }
        else if($today == "Thursday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [706,707,705,709]])
                    ->order(['Products.order' => 'asc']);  
        }
        else if($today == "Sunday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [706,707,708,705]])
                    ->order(['Products.order' => 'asc']);  
        }
        else
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [705,706,707,708,709]])
                    ->order(['Products.order' => 'asc']);  
        }

        return $products;
    }


    public function markham_product($location)
    {
        $today = date("l");
        $Products = TableRegistry::get('Products');
        if($today == "Monday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [637,638,639,640]])
                    ->order(['Products.order' => 'asc']);  
        }
        else if($today == "Tuesday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [636,638,639,640]])
                    ->order(['Products.order' => 'asc']);  
        }
        else if($today == "Wednesday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [637,636,639,640]])
                    ->order(['Products.order' => 'asc']);  
        }
        else if($today == "Thursday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [637,638,636]])
                    ->order(['Products.order' => 'asc']);  
        }
        else if($today == "Sunday")
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [637,638,639,636]])
                    ->order(['Products.order' => 'asc']);  
        }
        else
        {
          $products = $Products->find("all")->where(['Products.restaurants_id'=>$location,'Products.category_id !='=>MAKE_YOUR_OWN,'id NOT IN' => [636,637,638,639,640]])
                    ->order(['Products.order' => 'asc']);  
        }

        return $products;
    }

    public function combo_products_view1() {
        $tab_index = $_POST['tab_index'];
        $this->layout = false;
        $subcatArr = array();
        $productsArr = array();
        $maincategory_id = $_POST['maincategory_id'];
        $cat = array(PIZZA_COMBO_ID);
        $this->set('productsArr', $productsArr);
        $this->set('tab_index', $tab_index);
        $ComboCategories = TableRegistry::get('ComboCategories');
        $combocategories = $ComboCategories->find("all")
                ->where(['ComboCategories.id NOT IN' => $cat])
                ->all();
        foreach ($combocategories as $key => $value) {
            $pizza_combo_name = $value['name'];
            $pizza_combo_id = $value['id'];
        }
        $this->set('pizza_combo_name', $pizza_combo_name);
        $this->set('pizza_combo_id', $pizza_combo_id);

        $Combo = TableRegistry::get('Combo');
        $pizza_combo = $Combo->find("all")
                ->where(['Combo.combo_category NOT IN' => $cat])
                ->all();
        foreach ($pizza_combo as $key => $value) {
            $pizza_comboArr[] = array('id' => $value['id'], 'name' => $value['name'], 'image' => $value['image'], 'price' => $value['price']);
        }
        $this->set('pizza_comboArr', $pizza_comboArr);
        $ComboProducts = TableRegistry::get('ComboProducts');
        $pizza_combo_products = $ComboProducts->find("all");
        foreach ($pizza_combo_products as $key => $value) {
            $pizza_combo_productsArr[$value['combo_id']][] = array('combo_id' => $value['combo_id']);
        }
        $this->set('pizza_combo_productsArr', $pizza_combo_productsArr);

        $this->render('combo_products_view');
        //print_r($subcatArr);
        // exit();
    }

    public function products_addons($value = '') {
        $tab_index = $_POST['tab_index'];

        $this->set('tab_index', $tab_index);

        $product_id = $_POST['product_id'];
        $Products = TableRegistry::get('Products');
        $product_details = $Products->find()
                ->where(['Products.id' => $product_id])
                ->all();
        $this->set('product_details', $product_details);

        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => $product_id])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');
        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->all();
        $this->set('addons_all', $addons_all);
        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $selection = $AddonCategories->find()
                ->where(['AddonCategories.selection' => "single"])
                ->all();
        $this->set('selection', $selection);


        $addons = $Addons->find("all");
        $this->set('addons', $addons);
        $this->set('addon_catArr', $addon_catArr);
        $this->layout = false;
        $this->render('products_addons');
    }

    public function combo_products($value = '') {
        $tab_index = $_POST['tab_index'];
        $base_price = $_POST['base_price'];
        $this->set('base_price', $base_price);
        $this->set('tab_index', $tab_index);
        $product_name = $_POST['product_name'];
        $this->set('product_name', $product_name);
        $product_image = $_POST['product_image'];
        $this->set('product_image', $product_image);
        $combo_id = $_POST['product_id'];
        $this->set('combo_id', $combo_id);
        $subcategory_name = $_POST['subcategory_name'];
        $this->set('subcategory_name', $subcategory_name);
        $subcategory_id = $_POST['subcategory_id'];
        $this->set('subcategory_id', $subcategory_id);
        $maincategory_name = $_POST['maincategory_name'];
        $this->set('maincategory_name', $maincategory_name);
        $maincategory_id = $_POST['maincategory_id'];
        $this->set('maincategory_id', $maincategory_id);
        $ComboProducts = TableRegistry::get('ComboProducts');
        $pizza_combo_products = $ComboProducts->find("all")
                ->where(['ComboProducts.combo_id' => $combo_id])
                ->all();

        $this->set('pizza_combo_products', $pizza_combo_products);

        $Categories = TableRegistry::get('Categories');
        $categories = $Categories->find("all");
        $this->set('categories', $categories);
        $Products = TableRegistry::get('Products');
        $products = $Products->find("all");
        $this->set('products', $products);

        $this->layout = false;
        $this->render('combo_products');
    }

    public function wings_cart() {
//        print_r($_POST);
//        exit();
        $size_id="";
        $size = "";
        $addonsArr = array();
        $dipsauceFlavour=array();
        $Addons = TableRegistry::get('Addons');
        $addons = $Addons->find()->all();
        foreach ($addons as $key => $value) {
            $addonsArr[$value['id']] = array(
                'id' => $value['id'],
                'name' => $value['name'],
                'addon_category_id' => $value['addon_category_id'],
                'image' => $value['image'],
                'price' => $value['price'],
            );
        }
        $AddonCategories = TableRegistry::get('AddonCategories');
        $addon_cat = $AddonCategories->find("all");
        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $product_count = 1;
        if (isset($_POST['final_price'])) {
            $total = $_POST['final_price'];
        }

        if (isset($_POST['total_price'])) {
            $total = $_POST['total_price'];
        }

        if (isset($_POST['product_count']) && $_POST['product_count'] != "") {
            $product_count = $_POST['product_count'];
        }
        $sauce_instruction = "";
        if (isset($_POST['sauce_instruction'])) {
            $sauce_instruction = $_POST['sauce_instruction'];
        }
        $sauce_instruction = $_POST['sauce_instruction'];

        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_image = $_POST['product_image'];
        if ($product_image == "") {
            $product_image = "no_image1.png";
        }
        $subcategory_id = "";
        $subcategory_name = "";

        if (isset($_POST['maincategory_id']))
            {
            $maincategory_id = $_POST['maincategory_id'];
            $inital_price = $_POST['start_price'];
        }
        $product_type = "";
        $green_name = "";
        $sauce_option = "";
        if ($maincategory_id == WING_ID) {
            $product_type = $_POST['wings_type'];
			$sauce_option = $_POST['sauce_option'];
            $size = "";

          // new for sauce count......
            $product_type_id  = $_POST['drinks_count'];
           
          
            foreach ($product_type_id as $key=>$value)
            {
               if($value!=0):
		        $count=$value ;
		        $product_typeArr[] = $count." ".$key;
		        $dipsauceFlavour[$key]=$count;
               endif;
            }  
        }

          if ($maincategory_id == FINGERS_ID) {
               //$sauce_option = $_POST['sauce_option'];
          }



        if ($maincategory_id == APPETIZER_ID) {
            $size = "small";
            $size = $_POST['psize'];
            if ($product_id == APPETIZER_STRIPS_ID) {
                $product_type = implode(",", $_POST['apetizer_type']);
            } else {
                $product_type = $_POST['apetizer_type'];
            }


            // new for sauce count......
            $product_type_id  = $_POST['drinks_count'];
            foreach ($product_type_id as $key=>$value)
            {
               if($value!=0):
		        $count=$value ;
		        $product_typeArr[] = $count." ".$key;
		        $dipsauceFlavour[$key]=$count;
               endif;
            }  

        }
//        if($maincategory_id==SHRIMP_ID)
//        {
//            $product_type   = $_POST['shrimp_type'];
//        }
        if ($maincategory_id == SALAD_ID) {
            $sauce_instruction = "";
            $product_type = $_POST['salad_type'];
            $size = $_POST['salad_size'];


            /** additional modification for salads  ***/

             $green = explode(",", $_POST['green_id']);
	     $greenarray = array();
		 
		 if(count($green)>0){
	     foreach($green as $v)
		{
			//$pricecart= $value[$v]['price']; 
			$greenarray[] = array('addon_id' => $v, 'name' => $_POST['salad_green'][$v]['name'],'price' => $_POST['salad_green'][$v][$size]);

		}
		 }
	    $green_name = $greenarray;
		
		
            $dressname = array();
			
			if($_POST['dress_id']!=''){
            $dressname[] = array('addon_id' => $_POST['dress_id'], 'name' => $_POST['dress_name'],'price' => 0.00);
			}

	     $add_on = explode(",", $_POST['addon_id']);
	     $add_onarray = array();
		 
	   if(count($add_on)>0){
	     foreach($add_on as $v)
		{
			                                                           
			$add_onarray[] = array('addon_id' => $v, 'name' => $_POST['salad_addon'][$v]['name'],'price' => $_POST['salad_addon'][$v][$size]);

		}
		  }                                                   
           // $green_name = $_POST['green_name'];


        }
        if ($maincategory_id == BURGER_ID) {
            $sauce_instruction = "";
            $product_type = "";
        }
        $drinksFlavour=array();
		$drinksFlavourPrice  =array();
        if ($maincategory_id == DRINK_ID) {
            $product_type_id = $_POST['drinks_type'];
            foreach ($product_type_id as $key=>$value)
            {
                $count=$_POST['drinks_count_'.$value];
                $product_typeArr[] = $count." ".$_POST['drinkTypeid_'.$value];
                $drinksFlavour[$value]=$count;
				
				$drinking_price = $_POST['drinks_price_'.$value];
				$drinksFlavourPrice[$value]=$drinking_price;
            }
            
            $product_type=implode(',',$product_typeArr);
            $size_id = $_POST['drinks_size'];
            $size = $_POST['drinkSize_'.$size_id];
        }
		
		$burgerArr=  array();
		$popsFlavour=array();
		$popsFlavourPrice  =array();
		if ($maincategory_id == PLATTERCATEGORY_ID) {
            $product_type_id = $_POST['pops_type'];
            foreach ($product_type_id as $key=>$value)
            {
                $count=$_POST['pops_count_'.$value];
                $product_typeArr[] = $count." ".$_POST['popTypeid_'.$value];
               // $popsFlavour[$value]=$count;
				
				$drinking_price = $_POST['pops_price_'.$value];
				$popsFlavourPrice[$value]=$drinking_price;
				
				$popsFlavour[$value] = array(
                'id' => $value,
                'name' => $_POST['popTypeid_'.$value],
                'price' => $drinking_price,
				'size'  => 360
            );
				
				
            }
            
            $product_type=implode(',',$product_typeArr);
            $size_id = $_POST['pops_size'];
            $size = '';
			
			
			
			
           if (isset($_POST['burger_products'])) {
            
            foreach ($_POST['burger_products'] as $key=>$val)
            {
                $string = str_replace(' ', '', $val);
                //$count=$_POST['count_'.$string];
				$count = 1;
                $burgerArr[]=$_POST['burger_products'];

            } 
        }
        }

		
        $dipsFlavour=array();
		$dipsPrice  =array();
        if ($maincategory_id == DIPPINGSAUCECATEGORY_ID) {
            $product_type_id = $_POST['dips_type'];
            foreach ($product_type_id as $key=>$value)
            {
                $count=$_POST['drinks_count_'.$value];
                $product_typeArr[] = $count." ".$_POST['drinkTypeid_'.$value];
                $dipsFlavour[$value]=$count;
				
				$dipping_price = $_POST['drinks_price_'.$value];
				$dipsPrice[$value]=$dipping_price;
				
            }
            
            $product_type=implode(',',$product_typeArr);
            
        }

        if ($maincategory_id == DESSERT_ID) {
            $product_type = implode(',', $_POST['desert_type']);
        }

        if ($maincategory_id == BASKET_ID) {
            $product_type = "";
            $sauce_instruction = "";
            $size = "";

            if ($product_id == BASKETWINGS_ID) {
                $product_type = $_POST['wings_type'];
                $sauce_instruction = $_POST['sauce_instruction'];
            }

            if ($product_id == BASKETCHICKEN_ID) {
                $size = $_POST['bsize'];
            }


            // new for sauce count......
            $product_type_id  = $_POST['drinks_count'];
            foreach ($product_type_id as $key=>$value)
            {
               if($value!=0):
		        $count=$value ;
		        $product_typeArr[] = $count." ".$key;
		        $dipsauceFlavour[$key]=$count;
               endif;
            }  
        }

        if (($maincategory_id == FINGERS_ID) || ($maincategory_id == SHRIMP_ID) || ($maincategory_id == WRAP_ID)) {
            $product_type = "";
            $size = "";
            $sauce_instruction = "";


            // new for sauce count......
            $product_type_id  = $_POST['drinks_count'];
            foreach ($product_type_id as $key=>$value)
            {
               if($value!=0):
		        $count=$value ;
		        $product_typeArr[] = $count." ".$key;
		        $dipsauceFlavour[$key]=$count;
               endif;
            }  
        }



        if ($maincategory_id == FOOTLONG_ID) {
            if ($product_id == FOOTLONG_TEXAN_ID) {
                $product_type = $_POST['footlongs_type'];
            } else {
                $product_type = "";
            }

            $size = "";
            $sauce_instruction = "";
        }

        if ($maincategory_id == ITALIAN_ID) {

            $size = "";
            $sauce_instruction = "";
            $product_type = "";

            if ($product_id == ITALIAN_ALFREDO_ID) {
                $product_type = $_POST['italian_alfredo_type'];
            }

            if ($product_id == ITALIAN_SPAGHETTI_ID) {
                $product_type = $_POST['italian_spaghetti_type'];
            }

            if ($product_id == ITALIAN_PARMIGIANA_ID) {
                $product_type = $_POST['italian_parmigiana_type'];
            }
        }
        if ($maincategory_id == POUTINE_ID) {
            $size = $_POST['psize'];
            $sauce_instruction = "";
            $product_type = "";
        }
        $product_type_countArr=  array();
        if (isset($_POST['platter_products'])) {
            
            foreach ($_POST['platter_products'] as $key=>$val)
            {
                $string = str_replace(' ', '', $val);
                //$count=$_POST['count_'.$string];
				$count = 1;
                $product_type_countArr[$string]=$count;

            } 
            $product_type = implode(',', $_POST['platter_products']);

             // new for sauce count......
            $plater_product_type_id  = $_POST['drinks_count'];
            foreach ($plater_product_type_id as $key=>$value)
            {
               if($value!=0):
		        $count=$value ;
		        $product_typeArr[] = $count." ".$key;
		        $dipsauceFlavour[$key]=$count;
               endif;
            }  

            
        }
        $instruction = $_POST['instruction'];
        $pizza_count = "";
        if (($maincategory_id == PARTYCATEGORY_ID) || ($maincategory_id == GAMECATEGORY_ID)) {
            $size = $_POST['size'];
            $wid = $_POST['wings'];
            $product_type = $_POST['wings_' . $wid];
            if (isset($_POST['wings_price'])) {
                $instruction = 'Add Breaded Wings';
            }
            $pizza_count = $_POST['pizza_count'];
        }


     

        $session = $this->request->session();
        //$session->delete('Cartarray1');

        if (!$session->read('Cartarray1')) {

            //$session->delete('Cartarray1');
            $emptyArr = array();
            $emptyArr1 = array();

            $cartArr1 = array('custom' => $emptyArr, 'direct' => $emptyArr, 'pizza' => $emptyArr, 'combo' => $emptyArr, 'quick' => $quickArr, 'demo'=>$emptyArr);
            $session = new Session();
            $sessionData = $session->write('Cartarray1', $cartArr1);
        }

		//echo '<pre>';print_r($session->read('Cartarray1'));exit;
        // print_r($_POST['addons']);exit();
        //unset($addcatArr);
        $addcatArr = array();
        $add_cartArr = array();
        $aaddArr = array();
        $i = 0;
        if (isset($_POST['addons']) || ($maincategory_id == SALAD_ID) || ($maincategory_id == DRINK_ID) || ($maincategory_id == DIPPINGSAUCECATEGORY_ID)|| ($maincategory_id == DESSERT_ID) || ($maincategory_id == APPETIZER_ID) || ($maincategory_id == ITALIAN_ID) ||  ($maincategory_id == PLATTERCATEGORY_ID)) {

            foreach ($_POST['addons'] as $key => $value) {
                $k = 0;
                $dipsAddArr1 = array();
                $addcatArr[$i]['addon_cat'] = $key;
                $addcatArr[$i]['addon_catname'] = $addon_catArr[$key];

                foreach ($value as $key1 => $value1) { 
                    if (isset($value1['addon_id'])) {

                        if($size=="large")
                        {
                          $pricecart = $value1['lprice']; 
                        }
                        else
                        {
                           $pricecart= $value1['price'];  
                        }

                        $dipsAddArr1[]['addonnames'] = array('addon_id' => $value1['addon_id'], 'name' => $addonsArr[$value1['addon_id']]['name'], 'image' => $addonsArr[$value1['addon_id']]['image'], 'price' => $pricecart);
                       /* if($size=="large")
                        {
                          $dipsAddArr1[]['addonnames']['price']= $value1['lprice']; 
                        }
                        else
                        {
                           $dipsAddArr1[]['addonnames']['price']= $value1['price'];  
                        }*/
                    }
					//echo '<pre>';print_r($value);exit;
					
                } 
				//echo '<pre>';print_r($value);exit;
				
				$LASAGNA  = unserialize (LASAGNA_ID);
				$PANZER  = unserialize (PANZER_ROTTY_ID);
				//if($product_id==PANZER_ROTTY_ID || $product_id==LASAGNA_ID)
				if(in_array($product_id,$PANZER) || in_array($product_id,$LASAGNA))
					{
						if (isset($value['addon_id'])) 
						{
							foreach($value['addon_id'] as $v)
								{
									$pricecart= $value[$v]['price']; 
									$dipsAddArr1[]['addonnames'] = array('addon_id' => $v, 'name' => $value[$v]['name'], 'image' => '', 'price' => $pricecart,'type'=>$value[$v]['type']);
                      
								}
						}
					}
					 
					 
                $addcatArr[$i]['addon_subcat'] = $dipsAddArr1;
                $i++;
            }

            $i = 0;
            $car = array();

            $addonArr = array('addons' => $addcatArr);


            //print_r($addcatArr) ; exit();
            $addonArr['addon_category_id'] = $addon_category_id;
            $addonArr['final_price'] = $total;
            $addonArr['inital_price'] = $inital_price;
            $addonArr['product_image'] = $product_image;
            $addonArr['product_name'] = $product_name;
            $addonArr['product_id'] = $product_id;
            $addonArr['subcategory_name'] = $subcategory_name;
            $addonArr['subcategory_id'] = $subcategory_id;
            $addonArr['maincategory_id'] = $maincategory_id;
            $addonArr['size'] = $size;
            $addonArr['product_type'] = $product_type;
             $addonArr['product_type_count'] = $product_type_countArr;
            $addonArr['product_count'] = $product_count;
            $addonArr['sauce_instruction'] = $sauce_instruction;
            $addonArr['instruction'] = $instruction;
            $addonArr['green_name'] = $green_name;
            $addonArr['pizza_count'] = $pizza_count;
            $addonArr['size_id'] = $size_id;
            $addonArr['drinksFlavour'] = $drinksFlavour;
            $addonArr['dipsFlavour'] = $dipsFlavour;
	    $addonArr['dressname'] = $dressname;
	    $addonArr['add_on'] = $add_onarray;
			$addonArr['popsFlavour'] = $popsFlavour;

            $addonArr['dipsauceFlavour'] = $dipsauceFlavour;
			
			$addonArr['drinksFlvPrice'] = $drinksFlavourPrice;
			$addonArr['popsFlvPrice'] = $popsFlavourPrice;
			$addonArr['dipsFlvPrice'] = $dipsPrice;
			$addonArr['sub_product'] = $burgerArr;
			$addonArr['sauce_option'] = $sauce_option;


            
            //print_r($addonArr);exit;
            // $session = $this->request->session();
            // $session->delete('Cartarray1');
            // //print_r($cartArr); exit();
            $cart_items = array();
            $session = $this->request->session();
            if ($session->read('Cartarray1')) {
                //echo "string"; exit;
                $cart_items = $session->read('Cartarray1');

                $pizza_key = $_GET['key'];
                if ($pizza_key != "") {
                    unset($cart_items['custom'][$pizza_key]);
                    $session->delete('Cartarray1');
                    $session = new Session();
                    $sessionData = $session->write('Cartarray1', $cart_items);
                }
                $session = $this->request->session();
                $cart_items = $session->read('Cartarray1');

                //print_r($cart_items); exit;
                array_push($cart_items['custom'], $addonArr);
                $sessionData = $session->write('Cartarray1', $cart_items);
            } else {
                //echo "string1"; exit;
                $k = array();
                $cartArr = array($addcatArr);
                $cartArr1 = array('custom' => $cartArr, 'direct' => $k);

                $session = new Session();
                $sessionData = $session->write('Cartarray1', $cartArr1);
            }

            $src1 = $this->request->webroot;
            $src = $src1 . "products/" . $product_image;
//      $s="<div><img  height='50px;' class='cart_img' width='50px;' src='".$src."'>
//      ".$product_count." x " .$size." ".$product_name."<b>$ ".$total."</b>".
//        "</div>";

            $s = "<div class='added_component_box'><img  height='44px;' class='cart_img' width='44px;' src='" . $src . "'>
      <h3>" . $size . $product_name . "</h3><div class='added_price'>Price : $" . $total . "</div>
      <div class='count_box'>" . $product_count . "</div>";
            echo $s;
            exit;
            //print_r($addcatArr); exit();
        }


        $maincategory_id = $_POST['maincategory_id'];
        $maincategory_name = $_POST['maincategory_name'];

        $base_price = $_POST['base_price'];
        $total_price = $_POST['total_price'];

        if (isset($_POST['product_section']) && ($_POST['product_section'] == 'combo')) {
            $subcategory_id = $_POST['subcategory_id'];
            $subcategory_name = $_POST['subcategory_name'];
            $comboArr = array(
                'maincategory_id' => $maincategory_id,
                'maincategory_name' => $maincategory_name,
                'product_count' => $product_count,
                'product_id' => $product_id,
                'product_name' => $product_name,
                'product_image' => $product_image,
                'base_price' => $_POST['inital_price'],
                'total_price' => $total,
                'subcategory_id' => $subcategory_id,
                'subcategory_name' => $subcategory_name,
                'size' => $size
            );
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            array_push($cart_items['combo'], $comboArr);
            $sessionData = $session->write('Cartarray1', $cart_items);

            $src1 = $this->request->webroot;
            $src = $src1 . "products/" . $product_image;
//      $s="<div><img  height='50px;' class='cart_img' width='50px;' src='".$src."'>
//      ".$product_count." x " .$size." ".$product_name."<b>$ ".$total."</b>".
//        "</div>";

            $s = "<div class='added_component_box'><img  height='44px;' class='cart_img' width='44px;' src='" . $src . "'>
      <h3>" . $size . $product_name . "</h3><div class='added_price'>Price : $" . $total . "</div>
    <div class='count_box'>" . $product_count . "</div>";
            echo $s;
            exit;
        } else {
            $directArr = array(
                'maincategory_id' => $maincategory_id,
                'maincategory_name' => $maincategory_name,
                'product_count' => $product_count,
                'product_id' => $product_id,
                'product_name' => $product_name,
                'product_image' => $product_image,
                'base_price' => $base_price,
                'total_price' => $total_price,
                'subcategory_id' => $subcategory_id,
                'subcategory_name' => $subcategory_name,
                'size' => $size
            );

            //cartArr1=array('direct'=>$directArr);
            //$cartArr1['direct']=$directArr;
            // print_r($cartArr1); exit;
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            array_push($cart_items['direct'], $directArr);
            $sessionData = $session->write('Cartarray1', $cart_items);

            $src1 = $this->request->webroot;
            $src = $src1 . "products/" . $product_image;
//      $s="<div><img  height='50px;' class='cart_img' width='50px;' src='".$src."'>
//      ".$product_count." x " .$size."  ".$product_name."<b>$ ".$total."</b>".
//        "</div>";

            $s = "<div class='added_component_box'><img  height='44px;' class='cart_img' width='44px;' src='" . $src . "'>
      <h3>" . $size . $product_name . "</h3><div class='added_price'>Price : $" . $total . "</div>
    <div class='count_box'>" . $product_count . "</div>";
            echo $s;
            exit;
        }




        //print_r($_POST);exit();
    }

    public function cart() {
        //print_r($_POST);exit();
        $size = "";
        $AddonCategories = TableRegistry::get('AddonCategories');
        $addon_cat = $AddonCategories->find("all");
        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $product_count = 1;
        if (isset($_POST['final_price'])) {
            $total = $_POST['final_price'];
        }

        if (isset($_POST['total_price'])) {
            $total = $_POST['total_price'];
        }
        if (isset($_POST['size'])) {
            $size = $_POST['size'];
        }
        if (isset($_POST['product_count']) && $_POST['product_count'] != "") {
            $product_count = $_POST['product_count'];
        }

        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_image = $_POST['product_image'];
        if ($product_image == "") {
            $product_image = "no_image1.png";
        }
        $subcategory_id = "";
        $subcategory_name = "";
        if (isset($_GET['maincategory_id'])) {

            $maincategory_id = $_GET['maincategory_id'];
            $subcategory_id = $_GET['subcategory_id'];
            $subcategory_name = $_POST['subcategory_name'];
            $inital_price = $_POST['inital_price'];
            $final_price = $_POST['final_price'];
            $addon_category_id = $_GET['addon_category_id'];
        }

//      if (isset($_POST['subcategory_id']))
//      {
//      	$subcategory_id=$_POST['subcategory_id'];
//        $subcategory_name=$_POST['subcategory_name'];
//
//
//      }



        $session = $this->request->session();
        //$session->delete('Cartarray1');

        if (!$session->read('Cartarray1')) {

            //$session->delete('Cartarray1'); 
            $emptyArr = array();
            $emptyArr1 = array();

            $cartArr1 = array('custom' => $emptyArr, 'direct' => $emptyArr, 'pizza' => $emptyArr, 'combo' => $emptyArr, 'quick' => $quickArr, 'demo'=>$emptyArr);
            $session = new Session();
            $sessionData = $session->write('Cartarray1', $cartArr1);
        }

        // print_r($_POST['addons']);exit();
        //unset($addcatArr);
        $addcatArr = array();
        $add_cartArr = array();
        $aaddArr = array();
        $i = 0;
        if (isset($_POST['addons'])) {


            foreach ($_POST['addons'] as $key => $value) {
                if (isset($value['count'])) {
                    $count = $value['count'];
                    $price = $value['price'];
                } else {
                    $count = "";
                    $price = "";
                }
                //$aaddArr['addon_cat']=$value['addon_category_id'];
                $aaddArr[$value['addon_category_id']][] = array(
                    'id' => $value['id'], 'name' => $value['name'],
                    'image' => $value['image'], 'price' => $price,
                    'count' => $count, 'total' => $value['total'],
                    'addon_category_id' => $value['addon_category_id']);
            }
            $i = 0;
            $car = array();
            foreach ($aaddArr as $key => $value) {
                $k = 0;
                if (isset($addon_catArr[$key])) {
                    $addon_catname = $addon_catArr[$key];
                } else {
                    $addon_catname = "";
                }

                $addcatArr[$i]['addon_cat'] = $key;
                $addcatArr[$i]['addon_catname'] = $addon_catname;

                foreach ($value as $key1 => $value1) {
                    $car[$k]['addonnames'] = array('id' => $value1['id'], 'name' => $value1['name'],
                        'image' => $value1['image'], 'price' => $value1['price'],
                        'count' => $value1['count'], 'total' => $value1['total'],
                        'addon_category_id' => $value1['addon_category_id']);

                    $k++;
                }
                $addcatArr[$i]['addon_subcat'] = $car;
                $i++;
            }
            $addonArr = array('addons' => $addcatArr);


            //print_r($addcatArr) ; exit();
            $addonArr['addon_category_id'] = $addon_category_id;
            $addonArr['final_price'] = $final_price;
            $addonArr['inital_price'] = $inital_price;
            $addonArr['product_image'] = $product_image;
            $addonArr['product_name'] = $product_name;
            $addonArr['product_id'] = $product_id;
            $addonArr['subcategory_name'] = $subcategory_name;
            $addonArr['subcategory_id'] = $subcategory_id;
            $addonArr['maincategory_id'] = $maincategory_id;
            $addonArr['size'] = $size;




            // $session = $this->request->session();
            // $session->delete('Cartarray1'); 
            // //print_r($cartArr); exit();
            $cart_items = array();
            $session = $this->request->session();
            if ($session->read('Cartarray1')) {
                //echo "string"; exit;
                $cart_items = $session->read('Cartarray1');
                //print_r($cart_items); exit;
                array_push($cart_items['custom'], $addonArr);
                $sessionData = $session->write('Cartarray1', $cart_items);
            } else {
                //echo "string1"; exit;
                $k = array();
                $cartArr = array($addcatArr);
                $cartArr1 = array('custom' => $cartArr, 'direct' => $k);

                $session = new Session();
                $sessionData = $session->write('Cartarray1', $cartArr1);
            }

            $src1 = $this->request->webroot;
            $src = $src1 . "products/" . $product_image;
//      $s="<div><img  height='50px;' class='cart_img' width='50px;' src='".$src."'>
//      ".$product_count." x " .$size." ".$product_name."<b>$ ".$total."</b>".
//        "</div>";

            $s = "<div class='added_component_box'><img  height='44px;' class='cart_img' width='44px;' src='" . $src . "'>
      <h3>" . $size . $product_name . "</h3><div class='added_price'>Price : $" . $total . "</div>
      <div class='count_box'>" . $product_count . "</div>";
            echo $s;
            exit;
            //print_r($addcatArr); exit();
        }


        $maincategory_id = $_POST['maincategory_id'];
        $maincategory_name = $_POST['maincategory_name'];

        $base_price = $_POST['base_price'];
        $total_price = $_POST['total_price'];

        if (isset($_POST['product_section']) && ($_POST['product_section'] == 'combo')) {
            $subcategory_id = $_POST['subcategory_id'];
            $subcategory_name = $_POST['subcategory_name'];
            $comboArr = array(
                'maincategory_id' => $maincategory_id,
                'maincategory_name' => $maincategory_name,
                'product_count' => $product_count,
                'product_id' => $product_id,
                'product_name' => $product_name,
                'product_image' => $product_image,
                'base_price' => $_POST['inital_price'],
                'total_price' => $total,
                'subcategory_id' => $subcategory_id,
                'subcategory_name' => $subcategory_name,
                'size' => $size
            );
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            array_push($cart_items['combo'], $comboArr);
            $sessionData = $session->write('Cartarray1', $cart_items);

            $src1 = $this->request->webroot;
            $src = $src1 . "products/" . $product_image;
//      $s="<div><img  height='50px;' class='cart_img' width='50px;' src='".$src."'>
//      ".$product_count." x " .$size." ".$product_name."<b>$ ".$total."</b>".
//        "</div>";

            $s = "<div class='added_component_box'><img  height='44px;' class='cart_img' width='44px;' src='" . $src . "'>
      <h3>" . $size . $product_name . "</h3><div class='added_price'>Price : $" . $total . "</div>
    <div class='count_box'>" . $product_count . "</div>";
            echo $s;
            exit;
        } else {
            $directArr = array(
                'maincategory_id' => $maincategory_id,
                'maincategory_name' => $maincategory_name,
                'product_count' => $product_count,
                'product_id' => $product_id,
                'product_name' => $product_name,
                'product_image' => $product_image,
                'base_price' => $base_price,
                'total_price' => $total_price,
                'subcategory_id' => $subcategory_id,
                'subcategory_name' => $subcategory_name,
                'size' => $size
            );

            //cartArr1=array('direct'=>$directArr);
            //$cartArr1['direct']=$directArr;
            // print_r($cartArr1); exit;
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            array_push($cart_items['direct'], $directArr);
            $sessionData = $session->write('Cartarray1', $cart_items);

            $src1 = $this->request->webroot;
            $src = $src1 . "products/" . $product_image;
//      $s="<div><img  height='50px;' class='cart_img' width='50px;' src='".$src."'>
//      ".$product_count." x " .$size."  ".$product_name."<b>$ ".$total."</b>".
//        "</div>";

            $s = "<div class='added_component_box'><img  height='44px;' class='cart_img' width='44px;' src='" . $src . "'>
      <h3>" . $size . $product_name . "</h3><div class='added_price'>Price : $" . $total . "</div>
    <div class='count_box'>" . $product_count . "</div>";
            echo $s;
            exit;
        }




        //print_r($_POST);exit();
    }

    public function quick_cart() {
        $crust = "";
        $specialArr = array();
        $add_cartArr = array();
        $freeAddArr = array();
        $dipsAddArr = array();
        $emptyArr = array();
        $quickArr = array();
        $product_count = 1;
        $total_price = $_POST['price'];
        $product_id = $_POST['product_id'];
        $size = $_POST['size'];
        $Products = TableRegistry::get('Products');
        $pizza_products = $Products->find("all")
                ->where(['Products.id' => $product_id])
                ->all();

        foreach ($pizza_products as $value) {
            $product_image = $value['image'];
            $product_name = $value['name'];
            $maincategory_id = $value['main_category_id'];
            $subcategory_id = $value['category_id'];
            $dips = explode(",", $value['default_dips']);
            $spl = explode(",", $value['default_special']);
            $topp = explode(",", $value['default_toppings']);
            $default = explode(",", $value['default_addons']);
        }

        $addonsArr = array();
        $Addons = TableRegistry::get('Addons');
        $addons = $Addons->find()->all();
        foreach ($addons as $key => $value) {
            $addonsArr[$value['id']] = array(
                'id' => $value['id'],
                'name' => $value['name'],
                'addon_category_id' => $value['addon_category_id'],
                'image' => $value['image'],
                'price' => $value['price'],
            );
        }

        $addonCategoriesArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');
        $addonCategories = $AddonCategories->find()->all();
        foreach ($addonCategories as $key => $value) {
            $addonCategoriesArr[$value['id']] = $value['name'];
        }


        if ($product_image == "") {
            $product_image = "no_image1.png";
        }


        $sizeArr = array(
            'name' => $size,
        );

        if (isset($spl)) {
            $special = $spl;
            $specialArr = array(
                'id' => $special,
                'name' => $addonsArr[$special]['name'],
                'image' => $addonsArr[$special]['image']
            );
        }


        $i = 0;
        if (isset($dips)) {

            foreach ($dips as $key => $value) {
                $k = 0;
                $dipsAddArr1 = array();
                $dipsAddArr[$i]['addon_cat'] = $addonsArr[$value]['addon_category_id'];
                $dipsAddArr[$i]['addon_catname'] = $addonCategoriesArr[$addonsArr[$value]['addon_category_id']];
                $dipsAddArr1[]['addonnames'] = array('addon_id' => $value, 'name' => $addonsArr[$value]['name'], 'image' => $addonsArr[$value]['image'], 'price' => 0.00,'default'=>1);
                $dipsAddArr[$i]['addon_subcat'] = $dipsAddArr1;
                $i++;
            }
        }

        $aaddArr = array();
        $add_cartArr = array();
        $i = 0;
        if (isset($topp)) {
            foreach ($topp as $key => $value) {
                $k = 0;
                $topsAddArr1 = array();
                $add_cartArr[$i]['addon_cat'] = $addonsArr[$value]['addon_category_id'];
                $add_cartArr[$i]['addon_catname'] = $addonCategoriesArr[$addonsArr[$value]['addon_category_id']];
				
				$typeX = '1x';

                $topsAddArr1[]['addonnames'] = array('side' => "full", 'type' => $typeX, 'name' => $addonsArr[$value]['name'], 'id' => $value, 'price' => 0.00,'default'=>1,'quick'=>1);
                $add_cartArr[$i]['addon_subcat'] = $topsAddArr1;
                $i++;
            }
        }
        //print_r($dipsAddArr); exit;

        $cartArr = array("toppings" => $add_cartArr,
            "free_toppings" => $freeAddArr,
            "dips" => $dipsAddArr,
            "special" => $specialArr,
            "size" => $sizeArr,
            "crust" => $crust,
            "product_id" => $product_id,
            "product_count" => $product_count,
            "total_price" => $total_price,
            "product_name" => $product_name,
            "product_image" => $product_image,
            "default_addons" => $default,
            "start_price" => $total_price,
        );

        $session = $this->request->session();
        //$session->delete('Cartarray1');

        if (!$session->read('Cartarray1')) {

            //$session->delete('Cartarray1');


            $cartArr1 = array(
                'custom' => $emptyArr,
                'direct' => $emptyArr,
                'pizza' => $emptyArr,
                'quick' => $quickArr,
                'combo' => $emptyArr,
                 'demo'=>$emptyArr
            );
            $session = new Session();
            $sessionData = $session->write('Cartarray1', $cartArr1);
        }

        $session = $this->request->session();
        $cart_items = $session->read('Cartarray1');
        //print_r($cartArr); exit();

        array_push($cart_items['pizza'], $cartArr);
        $sessionData = $session->write('Cartarray1', $cart_items);


        $src1 = $this->request->webroot;
        $src = $src1 . "products/" . $product_image;
//            $s="<div><img  height='50px;' class='cart_img' width='50px;' src='".$src."'>
//      ".$product_count." x " .$product_name."<b>$ ".$total_price."</b>".
//                "</div>";


        $s = "<div class='added_component_box'><img  height='44px;' class='cart_img' width='44px;' src='" . $src . "'>
    <h3>" . $product_name . "</h3><div class='added_price'>Price : $" . $total_price . "</div>
    <div class='count_box'>" . $product_count . "</div>";

        echo $s;
        exit;
    }

    public function sub_category() {
        //echo $_POST;
        $id = $_POST['id'];
        $Categories = TableRegistry::get('Categories');
        $cat = $Categories->find()
                ->where(['Categories.parent_id' => $id])
                ->all();
        $opt = '<option value="' . $id. '">No sub-category</option>';
        foreach ($cat as $key => $value) {
            $opt.='<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
        }

//           if(PIZZA_ID==$id)
//           {
//                     $opt.='<option value="twin">Twin</option>';
//     
//           }
        echo $opt;
        exit();
        // $opt.='<option value="'.$v['id'].'">'.$v['qualification_name'].'</option>';
    }

    public function addons() {
        $cat = $_POST['cat'];
        if ($cat == "pizza") {

            $paddonCategoriesArr = array();
            $AddonCategories = TableRegistry::get('AddonCategories');
            $paddonCategories = $AddonCategories->find("all")
                    ->where(['type' => 'pizza']);
            foreach ($paddonCategories as $key => $value) {
                $paddonCategoriesArr[] = $value['id'];
            }
            $Addons = TableRegistry::get('Addons');
            $paddons = $Addons->find("all")
                    ->where(['addon_category_id IN' => $paddonCategoriesArr]);
            $opt = "";
            foreach ($paddons as $key => $value) {
                $opt .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
            }
            echo $opt;
            exit();
        } else {
            $oaddonCategoriesArr = array();
            $AddonCategories = TableRegistry::get('AddonCategories');
            $oaddonCategories = $AddonCategories->find("all")
                    ->where(['type' => 'other']);
            foreach ($oaddonCategories as $key => $value) {
                $oaddonCategoriesArr[] = $value['id'];
            }
            $oaddonCategoriesArr[] = 0;

            $Addons = TableRegistry::get('Addons');
            $oaddons = $Addons->find("all")
                    ->where(['addon_category_id IN' => $oaddonCategoriesArr]);
            $opt = "";
            foreach ($oaddons as $key => $value) {
                $opt .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
            }
            echo $opt;
            exit();
        }
    }

    public function product_list_all() {
        //echo $_POST;
        $id = $_POST['id'];

        $cat1 = $_POST['cat'];
        if ($cat1 == 0) {

            $Products = TableRegistry::get('Products');
            $cat = $Products->find()
                    ->where(['Products.main_category_id' => $id])
                    ->all();
            $opt = "";
            foreach ($cat as $key => $value) {
                $opt .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
            }
            echo $opt;
            exit();
        } else {

            $Products = TableRegistry::get('Products');
            $cat = $Products->find()
                    ->where(['Products.category_id' => $id])
                    ->all();
            $opt = "";
            foreach ($cat as $key => $value) {
                $opt .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
            }
            echo $opt;
            exit();
            // $opt.='<option value="'.$v['id'].'">'.$v['qualification_name'].'</option>';
        }
    }

    public function sub_addons() {
        //echo $_POST;
        $id = $_POST['id'];
        $Addons = TableRegistry::get('Addons');
        $cat = $Addons->find()
                ->where(['Addons.addon_category_id' => $id])
                ->all();
        $opt = "";
        foreach ($cat as $key => $value) {
            $opt.='<input type="checkbox" name="" value="' . $value['id'] . '">' . $value['name'];
        }
        echo $opt;
        exit();
        // $opt.='<option value="'.$v['id'].'">'.$v['qualification_name'].'</option>';
    }

    public function cart_products_addons($value = '') {
        $product_id = $_POST['product_id'];
        $key = $_POST['key'];
        $type = $_POST['type'];
        $session = $this->request->session();
        $cart_items = $session->read('Cartarray1');
        $sessionArr = array();
        $checkArr = array();
        foreach ($cart_items[$type][$key]['addons'] as $key3 => $addonsubcat) {
            foreach ($addonsubcat['addon_subcat'] as $key1 => $value) {

                $sessionArr[$value['addonnames']['id']] = array(
                    'id' => $value['addonnames']['id'],
                    'name' => $value['addonnames']['name'],
                    'image' => $value['addonnames']['image'],
                    'price' => $value['addonnames']['price'],
                    'count' => $value['addonnames']['count'],
                    'total' => $value['addonnames']['total'],
                );
                $checkArr[] = $value['addonnames']['id'];
            }
        }
        $this->set('sessionArr', $sessionArr);
        $this->set('checkArr', $checkArr);
        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => $product_id])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
//print_r($product_addons_list); exit();

        $Addons = TableRegistry::get('Addons');
        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->all();

        // $addons_all = $Addons->find()
        // ->where(['Addons.product_id' =>$product_id])
        // ->all();

        $this->set('addons_all', $addons_all);
        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $selection = $AddonCategories->find()
                ->where(['AddonCategories.selection' => "single"])
                ->all();
        $this->set('selection', $selection);

        //echo "hi";exit;
        $addons = $Addons->find("all");
        $this->set('addons', $addons);
        $this->set('addon_catArr', $addon_catArr);
        $this->layout = false;
        $this->render('cart_products_addons');
    }

    public function add_in_cart($value = '') {

        // print_r($_POST) ;
        // exit;
        $keys = $_POST['key'];
        $type = $_POST['addons'];
        $index = $_POST['index'];
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_image = $_POST['product_image'];
        $maincategory_id = $_POST['maincategory_id'];
        $subcategory_id = $_POST['subcategory_id'];
        $subcategory_name = $_POST['subcategory_name'];
        $inital_price = $_POST['inital_price'];
        $final_price = $_POST['final_price'];
        $product_count = $_POST['product_count'];
        $addon_category_id = $_POST['addon_category_id'];
        if (isset($_POST['addons'])) {


            foreach ($_POST['addons'] as $key => $value) {
                if (isset($value['count'])) {
                    $count = $value['count'];
                    $price = $value['price'];
                } else {
                    $count = "";
                    $price = "";
                }
                //$aaddArr['addon_cat']=$value['addon_category_id'];
                $aaddArr[$value['addon_category_id']][] = array(
                    'id' => $value['id'], 'name' => $value['name'],
                    'image' => $value['image'], 'price' => $price,
                    'count' => $count, 'total' => $value['total'],
                    'addon_category_id' => $value['addon_category_id']);
            }

            //print_r($aaddArr);exit;
            $i = 0;
            $car = array();
            foreach ($aaddArr as $key => $value) {
                $k = 0;

                $addcatArr[$i]['addon_cat'] = $key;

                foreach ($value as $key1 => $value1) {
                    $car[$k]['addonnames'] = array('id' => $value1['id'], 'name' => $value1['name'],
                        'image' => $value1['image'], 'price' => $value1['price'],
                        'count' => $value1['count'], 'total' => $value1['total'],
                        'addon_category_id' => $value1['addon_category_id']);

                    $k++;
                }
                $addcatArr[$i]['addon_subcat'] = $car;
                $i++;
            }

            $addonArr = array('addons' => $addcatArr);
            $addonArr['addon_category_id'] = $addon_category_id;
            $addonArr['final_price'] = $final_price;
            $addonArr['inital_price'] = $inital_price;
            $addonArr['product_image'] = $product_image;
            $addonArr['product_name'] = $product_name;
            $addonArr['product_id'] = $product_id;
            $addonArr['subcategory_name'] = $subcategory_name;
            $addonArr['subcategory_id'] = $subcategory_id;
            $addonArr['maincategory_id'] = $maincategory_id;
            $addonArr['product_count'] = $product_count;


            // print_r($addonArr) ; exit;
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            unset($cart_items['custom'][$keys]);
            //print_r($cart_items) ; exit;
            array_push($cart_items['custom'], $addonArr);
            $session->delete('Cartarray1');
            $session = new Session();
            $sessionData = $session->write('Cartarray1', $cart_items);
            $cart_items1 = $session->read('Cartarray1');
            print_r($cart_items1);
            exit;


//           $session = $this->request->session();
//          $cart_items= $session->read('Cartarray1'); 
//           //print_r($cart_items['custom'][$keys]['addons']);exit;
// $i=0;$k=0;
//          foreach ($cart_items['custom'][$keys]['addons'] as $key1 => $value) 
//          {
//              if(isset($aaddArr[$value['addon_cat']]))
//              {
//               //echo "e3ntr"; exit;
//                array_push($cart_items['custom'][$keys]['addons'][$i]['addon_subcat'],$aaddArr[$value['addon_cat']][$k]);
//              $k++;}
//          $i++;  }
//          $session->delete('Cartarray1');
//          $session = new Session();
//          $sessionData = $session->write('Cartarray1',$cart_items); 
//          $cart_items1= $session->read('Cartarray1');
//            print_r($cart_items1) ; exit;
        }
    }

    public function special_view() {
        // $tab_index=$_POST['tab_index'];
        // $this->layout=false;
        $subcatArr = array();
        $productsArr = array();
        $maincategory_id = $_GET['id'];
        $Categories = TableRegistry::get('Categories');
        $subcat = $Categories->find()
                ->where(['Categories.parent_id' => $maincategory_id])
                ->order(['Categories.order ' => 'asc'])
                ->all();

        foreach ($subcat as $key => $value) {
            $subcatArr[$value['id']] = $value['name'];
        }
        $Products = TableRegistry::get('Products');
        if (empty($subcatArr)) {
            $products = $Products->find("all")
                    ->where(['Products.category_id' => $maincategory_id])
                    ->order(['Products.order' => 'asc'])
                    ->all();
            foreach ($products as $key => $value) {
                $productsArr[] = array('id' => $value['id'], 'name' => $value['name'], 'description' => $value['description'],
                    'base_price' => $value['base_price'], 'image' => $value['image'], 'customize' => $value['customize'], 'large_price' => $value['large_price']);
            }
        } else {


//           	           $products = $Products->find("all")
//                           ->order(['Products.order'=>'asc']);
            $products = $Products->find("all")
                    ->order(['Products.order' => 'asc']);

            foreach ($products as $key => $value) {
                $productsArr[$value['category_id']][] = array('id' => $value['id'], 'name' => $value['name'], 'description' => $value['description'],
                    'base_price' => $value['base_price'], 'image' => $value['image'], 'customize' => $value['customize']);
            }
        }
        //print_r($productsArr);exit;

        $Sizes = TableRegistry::get('Sizes');
        $sizes = $Sizes->find("all");
        foreach ($sizes as $key => $value) {
            $sizeArr[$value['product_id']][] = array('id' => $value['id'], 'name' => $value['name'], 'size' => $value['size'], 'price' => $value['price']);
        }
        $this->set('sizeArr', $sizeArr);

        $maincategory_details = $Categories->find()
                ->where(['Categories.id' => $maincategory_id])
                ->first();
        $maincategory_name = $maincategory_details['name'];

        $this->set('maincategory_id', $maincategory_id);

        $this->set('maincategory_name', $maincategory_name);

        $this->set('subcatArr', $subcatArr);
        $this->set('productsArr', $productsArr);
        // $this->set('tab_index', $tab_index);	 		
        $ComboCategories = TableRegistry::get('ComboCategories');
        $combocategories = $ComboCategories->find("all")
                ->where(['ComboCategories.id' => PIZZA_COMBO_ID])
                ->all();
        foreach ($combocategories as $key => $value) {
            $pizza_combo_name = $value['name'];
        }
        $this->set('pizza_combo_name', $pizza_combo_name);
        $Combo = TableRegistry::get('Combo');
        $pizza_combo = $Combo->find("all")
                ->where(['Combo.combo_category' => PIZZA_COMBO_ID])
                ->all();
        foreach ($pizza_combo as $key => $value) {
            $pizza_comboArr[] = array('id' => $value['id'], 'name' => $value['name'], 'image' => $value['image'], 'price' => $value['price']);
        }
        $this->set('pizza_comboArr', $pizza_comboArr);
        $ComboProducts = TableRegistry::get('ComboProducts');
        $pizza_combo_products = $ComboProducts->find("all");
        foreach ($pizza_combo_products as $key => $value) {
            $pizza_combo_productsArr[$value['combo_id']][] = array('combo_id' => $value['combo_id']);
        }
        $this->set('pizza_combo_productsArr', $pizza_combo_productsArr);

        $maincategoryIdArr = array(POUTINE_ID, BASKET_ID, ITALIAN_ID, FOOTLONG_ID, WRAP_ID, APPETIZER_ID, BURGER_ID, WING_ID, FINGERS_ID, SHRIMP_ID, DRINK_ID, DESSERT_ID);
        //print_r($maincategoryIdArr); exit;
        $this->set('maincategoryIdArr', $maincategoryIdArr);

        $this->render('special_view');
        //print_r($subcatArr);
        // exit();
    }

    public function pizza_combo($id = null, $p2) {

        $sizeviewcheck=0;
        if ((isset($_GET['combo'])) &&($_GET['combo']=='twin'))
         {
           $sizeviewcheck=1;

         }

        $Sizes = TableRegistry::get('Sizes');
        $Products = TableRegistry::get('Products');
        $ProductsAll = $Products->find("all");
        foreach ($ProductsAll as $key => $value) {
            $ProductsArr[$value['id']] = $value['name'];
        }
        $Combo = TableRegistry::get('Combo');
        $product = $Combo->find("all")
                ->where(['Combo.id' => $id])
                ->all();

        $ComboProducts = TableRegistry::get('ComboProducts');
        $combo_products = $ComboProducts->find("all")
                ->where(['ComboProducts.combo_id' => $id])
                ->all();

        foreach ($combo_products as $key => $value) {
            $combo_productsArr[] = array('name' => $ProductsArr[$value['name']], 'combo_id' => $value['combo_id'], 'count' => $value['count'], 'product_category_id' => $value['product_category_id'], 'main_category_id' => $value['main_category_id'], 'size' => $value['size'], 'addons' => $value['addons'], 'id' => $value['name']);
        }
        $combo_in_sessioncheck = 0;
        $check_com = 0;
        $direct_check = 0;
        $combo_current_price = "";
        $combo_start_price = "";
        $combo_current_pizza_price = "";

        $session = $this->request->session();
        $cart_items = $session->read('Cartarray1');

        if (!isset($_GET['key']) && (!empty($cart_items['combo']))) {
            
            end($cart_items['combo']);
            $last_id = key($cart_items['combo']);
            if ($cart_items['combo'][$last_id]['combo_product_id'] == $id) {
                $check_com = 1;
                $combo_in_sessioncheck = 1;
                $combo_current_price = $cart_items['combo'][$last_id][$id][combo_details][final_price];
                $combo_start_price = $cart_items['combo'][$last_id][$id][combo_details][start_price];
                $combo_size = $cart_items['combo'][$last_id][$id][combo_details][size];

                if (isset($p2)) {
                    if ($p2 == 1) {
                        $combo_current_pizza_price = $cart_items['combo'][$last_id][$id][1][total_price];
                    }
                    if ($p2 == 2) {
                        $combo_current_pizza_price = $cart_items['combo'][$last_id][$id][0][total_price];
                    }
                }
            }
        }

        if (isset($_GET['key']))
         {
            $check_com = 1;
            $combo_in_sessioncheck = 1;
        }
        else
        {
            $sizeviewcheck=1;
        }


        $pizza_twin = 0;
        $pizza_id = '';
        $pizza_name = '';

        $pizza_selection = "";
        if ((isset($p2)) && ($p2 == "2")) {
            $sizeviewcheck=0;
            if ($check_com == 1) {
                $direct_check = 1;
            }
            $pizza_selection = 1;
            $pizza_twin = 2;
            $this->set('combo_productsArr', $combo_productsArr[1]);
            $pizza_name = $combo_productsArr[0]['name'];
            $pizza_id = $combo_productsArr[1]['id'];
        } else if ((isset($p2)) && ($p2 == "1")) {
            $sizeviewcheck=0;

            if ($check_com == 1) {
                $direct_check = 1;
            }
            $pizza_selection = 0;
            $pizza_twin = 1;
            $pizza_name = $combo_productsArr[1]['name'];
            $pizza_id = $combo_productsArr[0]['id'];
            $this->set('combo_productsArr', $combo_productsArr[0]);
        }
        $this->set('combo_in_sessioncheck', $combo_in_sessioncheck);
        $this->set('direct_check', $direct_check);


        $this->set('pizza_name', $pizza_name);
        $this->set('pizza_id', $pizza_id);
        $this->set('pizza_selection', $pizza_selection);

        $pizza1[name] = $combo_productsArr[0]['name'];
        $pizza1[id] = $combo_productsArr[0]['id'];
        $pizza1[product_category_id] = $combo_productsArr[0]['product_category_id'];
        $pizza1[main_category_id] = $combo_productsArr[0]['main_category_id'];
        $pizza1[size] = $combo_productsArr[0]['size'];
        $pizza1[addons] = $combo_productsArr[0]['addons'];

        $pizza2[name] = $combo_productsArr[1]['name'];
        $pizza2[id] = $combo_productsArr[1]['id'];
        $pizza2[product_category_id] = $combo_productsArr[1]['product_category_id'];
        $pizza2[main_category_id] = $combo_productsArr[1]['main_category_id'];
        $pizza2[size] = $combo_productsArr[1]['size'];
        $pizza2[addons] = $combo_productsArr[1]['addons'];


        $this->set('pizza1', $pizza1);
        $this->set('pizza2', $pizza2);




        $this->set('pizza_twin', $pizza_twin);

        //echo '<pre>';print_r($pizza1); exit();
        $AddonCategories = TableRegistry::get('AddonCategories');
        $Prices = TableRegistry::get('Prices');
        $addon_type_prices = $Prices->find("all")
                ->where(['Prices.product_id' => id])
                ->all();


        $Addons = TableRegistry::get('Addons');

        $addonCategories = $AddonCategories->find("all")
                ->order(['AddonCategories.order' => asc]);

        $addon_maincat = $AddonCategories->find()
                ->where(['AddonCategories.parent_id' => 0])
                ->all();

        $query = $Addons->find()
                ->order(['Addons.order' => asc])
                ->all();
        // echo '<pre>';print_r($addon_type_prices); exit();

        $addon_dips = $AddonCategories->find()
                ->where(['AddonCategories.id' => DIPS_ID])
                ->first();

        $addon_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => TOPPINGS_ID])
                ->first();

        $addon_free_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => FREE_TOPPINGS_ID])
                ->first();

        $addon_special = $AddonCategories->find()
                ->where(['AddonCategories.id' => SPECIAL_INSTRUCTION_ID])
                ->first();

        //  $size = $AddonCategories->find()
        // ->where(['AddonCategories.id' =>SIZE_ID ])
        // ->first();

        $this->set('addon_dips', $addon_dips);
        $this->set('addon_toppings', $addon_toppings);
        $this->set('addon_free_toppings', $addon_free_toppings);
        $this->set('addon_special', $addon_special);
        // $this->set('size', $size);



        $this->set('addon_type_prices', $addon_type_prices);

        $this->set('addon_maincat', $addon_maincat);
        $this->set('addonCategories', $addonCategories);
        $this->set('addons', $query);
        $this->set('product', $product);
        $this->set('_serialize', ['product']);



        $session_key = "";

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['combo'][$key][$id][$pizza_selection];
            $this->set('selected_items', $selected_items);
            $this->set('selected_itemArr', $cart_items['combo'][$key][$id]);
            // $this->set('session_key',$key);
            $session_key = $key;
                        //echo "<pre>";print_r($cart_items['combo'][$key][$id]); exit;

//        unset($cart_items['pizza'][$key]);
//        $session->delete('Cartarray1');
//        $session = new Session();
//        $sessionData = $session->write('Cartarray1',$cart_items);
            $editComboInitalPrice = $cart_items['combo'][$key][$id][combo_details][final_price];
            //$combo_current_price=$editComboInitalPrice;
            $this->set('editComboInitalPrice', $editComboInitalPrice);
            $combo_start_price = $cart_items['combo'][$key][$id][combo_details][start_price];

            $combo_size = $cart_items['combo'][$key][$id][combo_details][size];

            if (isset($p2)) {
                if ($p2 == 1) {
                    $combo_current_pizza_price = $cart_items['combo'][$key][$id][1][total_price];
                }
                if ($p2 == 2) {
                    $combo_current_pizza_price = $cart_items['combo'][$key][$id][0][total_price];
                }
            }
        }
        //echo $combo_current_pizza_price; exit;
        $combo_current_price = $combo_current_pizza_price + $combo_start_price;

        $this->set('combo_current_price', $combo_current_price);
        $this->set('combo_size', $combo_size);

        $this->set('session_key', $session_key);
        $this->set('sizeviewcheck', $sizeviewcheck);

    }

    public function platter_details($id = null) {
        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => $id])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');


        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->order(['Addons.order' => asc])
                ->all();

        $this->set('addons_all', $addons_all);


        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $selection = $AddonCategories->find()
                ->where(['AddonCategories.selection' => "single"])
                ->all();
        $this->set('selection', $selection);


        $addons = $Addons->find()
                ->order(['Addons.order' => asc])
                ->all();
        $this->set('addons', $addons);
        $this->set('addon_catArr', $addon_catArr);
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Addons']
        ]);

        // echo $product['category_id'];exit;

        if ((BASKET_ID == $product['category_id'])) {
            $addons_side = $Addons->find()
                    ->where(['Addons.addon_category_id ' => BSIDE_ID])
                    ->all();
            $this->set('addons_side', $addons_side);
        }


        $this->set('product', $product);
        $this->set('_serialize', ['product']);

        $Combos = TableRegistry::get('Combos');
        $platter_details = $Combos->find()
                ->where(['Combos.type' => 'platter'])
                ->order(['Combos.id' => asc])
                ->all();

        $this->set('platter_details', $platter_details);


        $query = $Combos->find();
        $concat = $query->func()->max('Combos.count');
        $platter_details1 = $query->select(['count' => $concat])
                ->where(['type' => 'platter']);
        foreach ($platter_details1 as $key => $value) {
            $max_pdt_count = $value['count'];
        }
        $this->set('max_pdt_count', $max_pdt_count);



        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['custom'][$key];
            //echo "<pre>";print_r($selected_items); exit;
            $this->set('selected_items', $selected_items);
            $this->set('session_key', $key);

//        unset($cart_items['pizza'][$key]);
//        $session->delete('Cartarray1');
//        $session = new Session();
//        $sessionData = $session->write('Cartarray1',$cart_items);
        }
    }

    /*public function platter_price() {
        // echo  $_POST['size'];exit;
        //print_r($_POST);exit();
        $maincategory_id = $_POST['maincategory_id'];

        $product_count = $_POST['product_count'];
        $product_id = $_POST['product_id'];
        $platter_count = $_POST['platter_count'];
        $sauce_count = $_POST['sauce_count'];
        $max_sauce_count = $_POST['max_sauce_count'];


        $sizeCount      = 1;
        $sizeCounttotal = 0;
        $totalFlag      = True;


        $Combos = TableRegistry::get('Combos');
        $addon_price_details = $Combos->find()
                ->where(['Combos.type' => 'platter', 'Combos.count' => $platter_count])
                ->first();
        if (isset($addon_price_details['price'])) {
            $total = $addon_price_details['price'];
        }


        $i = 1;
        if (isset($_POST['addons'])) {
            foreach ($_POST['addons'] as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    if (isset($value1['addon_id'])) {

                         /*** for counting sauce after no. of defaults***/

                          /*  $totsize     = $_POST['drinks_count'][$value1['addon_id']];
	                    if($totsize!='' && $totsize!=0):
	                       $sizeCount = $totsize;
	                    endif;
                            $sizeCounttotal += $sizeCount;
                          
                            if($sizeCounttotal>$max_sauce_count && $totalFlag == true)
                             {
                                 $sizeCount = $sizeCounttotal-$max_sauce_count;
                                 $totalFlag = false;
                             }
                              
                            /**********************/



                        //if ($i > $max_sauce_count) {
                      /*  if ($sizeCounttotal > $max_sauce_count) {
                            //$addon_price = $value1['price'];

                            $addon_price = $value1['price']* $sizeCount;
                            $total+=$addon_price;
                        }


                        $i++;
                    }
                }
            }
        }






        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        print_r($final);
        exit();

        //print_r($_POST); exit();
    }*/

    public function platter_sauce() {
        $platter_count = $_POST['platter_count'];
        $sauce_count = $_POST['sauce_count'];
        //$total=$_POST['start_price'];

        $Combos = TableRegistry::get('Combos');
        $addon_price_details = $Combos->find()
                ->where(['Combos.type' => 'platter', 'Combos.count' => $platter_count])
                ->first();
        if (isset($addon_price_details['sauce_count'])) {
            $total = $addon_price_details['sauce_count'];
        }
//            if($total=="")
//            {
//               $total=2; 
//            }
        echo $total;
        exit;
    }

    public function party_details_old($id = null,$productType = null, $p2 = null)
     {
        $session_edit_key="";
        $add_edit_check="add";
        if($_GET['combo']=="party")
        {
            $key=$_GET['key'];
            $session_edit_key=$key;
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
             $cart_items['demo'][$key]=$cart_items['combo'][$key];
            // array_push($cart_items['demo'],$cart_items['combo'][$key]);
            $sessionData = $session->write('Cartarray1',$cart_items);
            $add_edit_check="edit";

        }
        if($_GET['com']=="party")
        {
            $key=$_GET['key'];
            $add_edit_check="edit";
            $session_edit_key=$key;
        }
         // echo '<pre>';print_r($cart_items);exit;

        $this->set('add_edit_check', $add_edit_check);
        $this->set('session_edit_key', $session_edit_key);

        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => 259])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');


        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->order(['Addons.order' => asc])
                ->all();

        $this->set('addons_all', $addons_all);

        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }
        $this->set('addon_catArr', $addon_catArr);



        $Sizes = TableRegistry::get('Sizes');
        $Products = TableRegistry::get('Products');
        $ProductsAll = $Products->find("all");
        foreach ($ProductsAll as $key => $value) {
            $ProductsArr[$value['id']] = $value['name'];
        }
        $Combo = TableRegistry::get('Combo');
        $product = $Combo->find("all")
                ->where(['Combo.id' => $id])
                ->all();
        $combo_category=COMBOPARTYID;
        $this->set('combo_category', $combo_category);

        // $ComboProducts = TableRegistry::get('ComboProducts');
        // $combo_products = $ComboProducts->find("all")
        //         ->where(['ComboProducts.combo_id' => $id])
        //         ->all();

        
        $combo_in_sessioncheck = 0;
        $check_com = 0;
        $direct_check = 0;
        $combo_current_price = "";
        $combo_start_price = "";
        $combo_current_pizza_price = "";

        $session = $this->request->session();
        $cart_items = $session->read('Cartarray1');

        if (!isset($_GET['key']) && (!empty($cart_items['demo']))) {
            end($cart_items['demo']);
            $last_id = key($cart_items['demo']);
            if ($cart_items['demo'][$last_id]['combo_product_id'] == $id) {
                $check_com = 1;
                $combo_in_sessioncheck = 1;
                $combo_current_price = $cart_items['demo'][$last_id][$id][combo_details][final_price];
                $combo_start_price = $cart_items['demo'][$last_id][$id][combo_details][start_price];
                $combo_size = $cart_items['demo'][$last_id][$id][combo_details][size];

                if (isset($p2)) {
                   $index=$p2-1; 
                $combo_current_pizza_price = $cart_items['demo'][$last_id][$id][$productType][$index][total_price];

                }
            }
        }

        if (isset($_GET['key'])) {
            $check_com = 1;
            $combo_in_sessioncheck = 1;
        }


        $pizza_twin = 0;
        $pizza_id = '';
        $pizza_name = '';

        $pizza_selection = "";
        if (isset($p2)) 
         {

            if ($check_com == 1) 
            {
                $direct_check = 1;
            }
            $pizza_selection = $p2-1;
            $pizza_twin = $p2;
            $pizza_name = $productType." ".$p2;
            $pizza_id = "";
        }
        $this->set('combo_in_sessioncheck', $combo_in_sessioncheck);
        $this->set('direct_check', $direct_check);


        $this->set('pizza_name', $pizza_name);
        $this->set('pizza_id', $pizza_id);
        $this->set('pizza_selection', $pizza_selection);
        $this->set('productType', $productType);


        $this->set('pizza_twin', $pizza_twin);

        //echo '<pre>';print_r($pizza1); exit();
        $AddonCategories = TableRegistry::get('AddonCategories');
        $Prices = TableRegistry::get('Prices');
        $addon_type_prices = $Prices->find("all")
                ->where(['Prices.product_id' => id])
                ->all();


        $Addons = TableRegistry::get('Addons');

        $addonCategories = $AddonCategories->find("all")
                ->order(['AddonCategories.order' => asc]);

        $addon_maincat = $AddonCategories->find()
                ->where(['AddonCategories.parent_id' => 0])
                ->all();

        $query = $Addons->find()
                ->order(['Addons.order' => asc])
                ->all();
        // echo '<pre>';print_r($addon_type_prices); exit();

        $addon_dips = $AddonCategories->find()
                ->where(['AddonCategories.id' => DIPS_ID])
                ->first();

        $addon_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => TOPPINGS_ID])
                ->first();

        $addon_free_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => FREE_TOPPINGS_ID])
                ->first();

        $addon_special = $AddonCategories->find()
                ->where(['AddonCategories.id' => SPECIAL_INSTRUCTION_ID])
                ->first();

        //  $size = $AddonCategories->find()
        // ->where(['AddonCategories.id' =>SIZE_ID ])
        // ->first();

        $this->set('addon_dips', $addon_dips);
        $this->set('addon_toppings', $addon_toppings);
        $this->set('addon_free_toppings', $addon_free_toppings);
        $this->set('addon_special', $addon_special);
        // $this->set('size', $size);



        $this->set('addon_type_prices', $addon_type_prices);

        $this->set('addon_maincat', $addon_maincat);
        $this->set('addonCategories', $addonCategories);
        $this->set('addons', $query);
        $this->set('product', $product);
        $this->set('_serialize', ['product']);



        $session_key = "";

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['demo'][$key][$id][$productType][$pizza_selection];
            
            $this->set('selected_itemArr', $cart_items['demo'][$key][$id]);
            $session_key = $key;
            if($productType=="wings")
            {
              $selected_items = $cart_items['demo'][$key][$id][$productType];
            }
            $this->set('selected_items', $selected_items);

			
            $editComboInitalPrice = $cart_items['demo'][$key][$id][combo_details][final_price];
            //$combo_current_price=$editComboInitalPrice;
            $this->set('editComboInitalPrice', $editComboInitalPrice);
            $combo_start_price = $cart_items['demo'][$key][$id][combo_details][start_price];

            $combo_size = $cart_items['demo'][$key][$id][combo_details][size];

            if (isset($p2)) {
                                    $index=$p2-1;

                    $combo_current_pizza_price = $cart_items['demo'][$key][$id][$index][total_price];

                // if ($p2 == 1) {
                //     $index=$p2-1;
                //     $combo_current_pizza_price = $cart_items['combo'][$key][$id][1][total_price];
                // }
                // if ($p2 == 2) {
                //     $combo_current_pizza_price = $cart_items['combo'][$key][$id][0][total_price];
                // }
            }
            $combo_current_price=$combo_start_price;
            if($productType=="pizza")
            {
                foreach ($cart_items['demo'][$key][$id][pizza] as $ind =>$value) 
                {
                    if($ind!=$index)
                    {
                        $combo_current_price+=$value['total_price'];
                    }
                    
                }

                $combo_current_price+=$cart_items['demo'][$key][$id][wings]['final_price'];
             }
             else
             {
                foreach ($cart_items['demo'][$key][$id][pizza] as $ind =>$value) 
                {
                    
                        $combo_current_price+=$value['total_price'];
                    
                }

             }
        }

        if($productType=="pizza")
        {
            $ComboProducts = TableRegistry::get('ComboProducts');
            $combo_products = $ComboProducts->find("all")
                ->where(['ComboProducts.combo_id' => $id,'ComboProducts.product_category_id' => PIZZA_ID,'ComboProducts.size' => $p2])
                ->all();
            foreach ($combo_products as $key => $value) 
            {
             $PizzaDefaultAddonsArr=explode(',',$value['addons']);
            }
            $this->set('default_addons', $PizzaDefaultAddonsArr);
            //echo '<pre>';print_r($PizzaDefaultAddonsArr); exit;


        }

        // $combo_current_price = $combo_current_pizza_price + $combo_start_price;
// echo  $combo_size;exit;
        $this->set('combo_current_price', $combo_current_price);
        $this->set('combo_size', $combo_size);

        $this->set('session_key', $session_key);

        $Addons = TableRegistry::get('Addons');
        $oaddons = $Addons->find("all")
                ->where(['Addons.addon_category_id' => PARTYWINGADDONCATEGORY_ID]);
        foreach ($oaddons as $key => $value) {
            $oaddonsArr[$value['id']] = $value['name'];
        }
        $this->set('oaddonsArr', $oaddonsArr);
    }

    public function party_price() {

        $sauce_count = $_POST['sauce_count'];
        $max_sauce_count = $_POST['max_sauce_count'];
        $combo_id = '';
        $maincategory_id = $_POST['maincategory_id'];
        $price_last = $_POST['price_last'];
        $product_count = $_POST['product_count'];
        $product_id = $_POST['product_id'];
        $size = $_POST['size'];
        $pizza_count = $_POST['pizza_count'];
        $wings_price = $_POST['wings_price'];
        if (isset($_POST['wings'])) {
            $wings = $_POST['wings'];
        } else {
            $wings = '';
        }
        $combo_type = $_POST['combo_type'];
        $Combos = TableRegistry::get('Combos');
        if ($price_last == 0) {
            $addon_price_details = $Combos->find()
                    ->where(['Combos.type' => $combo_type, 'Combos.size' => $size, 'Combos.pizza_count' => $pizza_count, 'Combos.wings' => $wings])
                    ->last();
            if (isset($addon_price_details['price'])) {
                $total = $addon_price_details['price'];
                $combo_id = $addon_price_details['id'];

                if (isset($_POST['wings_price'])) {
                    $total+=$addon_price_details['wings_price'];
                }
            }
        } else {
            $addon_price_details = $Combos->find()
                    ->where(['Combos.type' => $combo_type, 'Combos.size' => $size, 'Combos.pizza_count' => $pizza_count, 'Combos.wings' => $wings])
                    ->first();
            if (isset($addon_price_details['price'])) {
                $total = $addon_price_details['price'];
                $combo_id = $addon_price_details['id'];

                if (isset($_POST['wings_price'])) {
                    $total+=$addon_price_details['wings_price'];
                }
            }
        }




        $i = 1;

        if (isset($_POST['addons'])) {
            foreach ($_POST['addons'] as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    if (isset($value1['addon_id'])) {
                        if ($i > $max_sauce_count) {
                            $addon_price = $value1['price'];
                            $total+=$addon_price;
                        }


                        $i++;
                    }
                }
            }
        }






        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        // print_r($final) ; exit();
        //print_r($_POST); exit();
        $ff = array('total' => $final, 'id' => $combo_id);
        $ff = json_encode($ff);
        echo $ff;
        exit;
    }

    public function party_toppings() {
        $combo_type = $_POST['combo_type'];
        $size = $_POST['size'];
        $pizza_count = $_POST['pizza_count'];
        $Combos = TableRegistry::get('Combos');
        $addon_price_details = $Combos->find()
                ->where(['Combos.type' => $combo_type, 'Combos.pizza_count' => $pizza_count, 'Combos.size' => $size])
                ->first();
        if (isset($addon_price_details['count'])) {
            $total = $addon_price_details['count'];
        }
//            if($total=="")
//            {
//               $total=2; 
//            }
        echo $total;
        exit;
    }

    public function refresh_deals() {
        $product_id = $_POST['product_id'];

        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => $product_id])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');


        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->order(['Addons.order' => asc])
                ->all();

        $this->set('addons_all', $addons_all);

        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $this->set('addon_catArr', $addon_catArr);
        $this->layout = false;

        $size = $_POST['size'];
        $combo_type = $_POST['combo_type'];




        $Combos = TableRegistry::get('Combos');
        $party_detailsArr = $Combos->find()
                ->where(['Combos.type' => $combo_type, 'Combos.size' => $size])
                ->order(['Combos.id' => asc])
                ->all();
        foreach ($party_detailsArr as $key => $value) {
            $pizza_countArr[] = $value['pizza_count'];
        }


        foreach ($party_detailsArr as $key => $value) {
            $wingsidArr[] = $value['wings'];
        }

        $Addons = TableRegistry::get('Addons');
        $addonsArr = $Addons->find()
                ->where(['Addons.id IN' => $wingsidArr])
                ->all();

        foreach ($addonsArr as $key => $value) {
            $wingsArr[] = array('id' => $value['id'], 'name' => $value['name']);
        }
        //print_r($wingsArr); exit;

        $this->set('pizza_countArr', array_unique($pizza_countArr));
        $this->set('wingsArr', $wingsArr);

        $this->render('refresh_deals');
    }

    public function refresh_wings() {
        $combo_type = $_POST['combo_type'];
        $this->layout = false;
        if (isset($_POST['pizza_count'])) {
            $pizza_count = $_POST['pizza_count'];
            $size = $_POST['size'];
            $Combos = TableRegistry::get('Combos');
            $party_detailsArr = $Combos->find()
                    ->where(['Combos.type' => $combo_type, 'Combos.size' => $size, 'Combos.pizza_count' => $pizza_count])
                    ->order(['Combos.id' => asc])
                    ->all();
        }
        foreach ($party_detailsArr as $key => $value) {
            $wingsidArr[] = $value['wings'];
        }

        $Addons = TableRegistry::get('Addons');
        $addonsArr = $Addons->find()
                ->where(['Addons.id IN' => $wingsidArr])
                ->all();

        foreach ($addonsArr as $key => $value) {
            $wingsArr[] = array('id' => $value['id'], 'name' => $value['name']);
        }
        //print_r($wingsArr); exit;

        $this->set('pizza_countArr', array_unique($pizza_countArr));
        $this->set('wingsArr', $wingsArr);

        $this->render('refresh_wings');
    }

    public function refresh_toppings() {
        $size = $_POST['size'];
        $pizza_count = $_POST['pizza_count'];
        $wings = $_POST['wings'];

        if (($size == 'large') && ($pizza_count == '4') && ($wings == PARTYNOWINGSADDON_ID)) {

            $this->layout = false;
            $product_id = $_POST['product_id'];

            $product_addons_list = array();
            $ProductAddons = TableRegistry::get('ProductAddons');
            $product_addons = $ProductAddons->find()
                    ->where(['ProductAddons.product_id' => $product_id])
                    ->all();
            foreach ($product_addons as $key => $value) {
                $product_addons_list[] = $value['addon_id'];
            }
            $Addons = TableRegistry::get('Addons');


            $addons_all = $Addons->find()
                    ->where(['Addons.id IN' => $product_addons_list])
                    ->order(['Addons.order' => asc])
                    ->all();

            $this->set('addons_all', $addons_all);

            $addon_catArr = array();
            $AddonCategories = TableRegistry::get('AddonCategories');

            $addon_cat = $AddonCategories->find("all");

            foreach ($addon_cat as $key => $value) {
                $addon_catArr[$value['id']] = $value['name'];
            }

            $this->set('addon_catArr', $addon_catArr);
            $this->layout = false;



            $Addons = TableRegistry::get('Addons');
            $addons = $Addons->find("all");
            $this->set('addons', $addons);

//               $this->set('pizza_countArr', array_unique($pizza_countArr));
//               $this->set('wingsArr', $wingsArr);

            $this->render('refresh_toppings');
        } else {
            echo "fail";
            exit;
        }
    }

    public function game_details($id = null) {
        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => $id])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');


        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->order(['Addons.order' => asc])
                ->all();

        $this->set('addons_all', $addons_all);


        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $selection = $AddonCategories->find()
                ->where(['AddonCategories.selection' => "single"])
                ->all();
        $this->set('selection', $selection);


        $addons = $Addons->find()
                ->order(['Addons.order' => asc])
                ->all();
        $this->set('addons', $addons);
        $this->set('addon_catArr', $addon_catArr);
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Addons']
        ]);

        // echo $product['category_id'];exit;

        if ((BASKET_ID == $product['category_id'])) {
            $addons_side = $Addons->find()
                    ->where(['Addons.addon_category_id ' => BSIDE_ID])
                    ->all();
            $this->set('addons_side', $addons_side);
        }


        $this->set('product', $product);
        $this->set('_serialize', ['product']);

        $Combos = TableRegistry::get('Combos');
        $party_detailsArr = $Combos->find()
                ->where(['Combos.type' => 'game'])
                ->order(['Combos.id' => asc])
                ->all();
        foreach ($party_detailsArr as $key => $value) {
            $pizza_countArr[] = $value['pizza_count'];
        }







        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['custom'][$key];
            //echo "<pre>";print_r($selected_items); exit;
            $this->set('selected_items', $selected_items);
            $this->set('session_key', $key);

            $size = $selected_items['size'];
            $pizza_count = $selected_items['pizza_count'];
            $Combos = TableRegistry::get('Combos');
            $addon_price_details = $Combos->find()
                    ->where(['Combos.type' => 'game', 'Combos.pizza_count' => $pizza_count, 'Combos.size' => $size])
                    ->first();
            $max_sauce_count = $addon_price_details['count'];
            $this->set('max_sauce_count', $max_sauce_count);
            $pizza_countArr = array();
            $party_detailsArr = $Combos->find()
                    ->where(['Combos.type' => 'game', 'Combos.size' => $size])
                    ->order(['Combos.id' => asc])
                    ->all();
            foreach ($party_detailsArr as $key => $value) {
                $pizza_countArr[] = $value['pizza_count'];
            }

            $party_detailsArr = $Combos->find()
                    ->where(['Combos.type' => 'game', 'Combos.size' => $size, 'Combos.pizza_count' => $pizza_count])
                    ->order(['Combos.id' => asc])
                    ->all();



            foreach ($party_detailsArr as $key => $value) {
                $wingsidArr[] = $value['wings'];
            }

            $Addons = TableRegistry::get('Addons');
            $addonsArr = $Addons->find()
                    ->where(['Addons.id IN' => $wingsidArr])
                    ->all();

            foreach ($addonsArr as $key => $value) {
                $wingsArr[] = array('id' => $value['id'], 'name' => $value['name']);
            }


//        unset($cart_items['pizza'][$key]);
//        $session->delete('Cartarray1');
//        $session = new Session();
//        $sessionData = $session->write('Cartarray1',$cart_items);
        } else {
            foreach ($party_detailsArr as $key => $value) {
                $wingsidArr[] = $value['wings'];
            }

            $Addons = TableRegistry::get('Addons');
            $addonsArr = $Addons->find()
                    ->where(['Addons.id IN' => $wingsidArr])
                    ->all();

            foreach ($addonsArr as $key => $value) {
                $wingsArr[] = array('id' => $value['id'], 'name' => $value['name']);
            }
        }

        // print_r(array_unique($pizza_countArr)) ; exit;
        $this->set('wingsArr', $wingsArr);
        $this->set('pizza_countArr', array_unique($pizza_countArr));

        $Combos = TableRegistry::get('Combos');
        $game_details = $Combos->find()
                ->where(['Combos.type' => 'game'])
                ->order(['Combos.id' => asc])
                ->all();

        $this->set('game_details', $game_details);

        $Addons = TableRegistry::get('Addons');
        $oaddons = $Addons->find("all")
                ->where(['Addons.addon_category_id' => PARTYWINGADDONCATEGORY_ID]);
        foreach ($oaddons as $key => $value) {
            $oaddonsArr[$value['id']] = $value['name'];
        }
        $this->set('oaddonsArr', $oaddonsArr);
    }
    
    public function refresh_flavours() {
        
       // print_r($_POST) ;exit;
	    $delType = $this->request->session()->read('Config.deltype');
		$delLoc  = $this->request->session()->read('Config.location');
		
        $drinks_size = $_POST['drinks_size'];
        //$drinks_id = $_POST['drinks_sizeArr'][$drinks_size];
        $this->layout = false;
            
            $Combos = TableRegistry::get('Combos');
            $party_detailsArr = $Combos->find()
                    ->where(['Combos.type' => 'drink', 'Combos.size' => $drinks_size,'Combos.restaurants_id'=>$delLoc ,'name <>'=>NO_POP])
                    ->order(['Combos.id' => asc])
                    ->all();
       
        foreach ($party_detailsArr as $key => $value) {
            $nameidArr[] = $value['name'];
        }

        $Addons = TableRegistry::get('Addons');
        $addonsArr = $Addons->find()
                ->where(['Addons.id IN' => $nameidArr])
                ->all();

        foreach ($addonsArr as $key => $value) {
            $wingsArr[] = array('id' => $value['id'], 'name' => $value['name']);
        }
        //print_r($wingsArr); exit;

        $this->set('pizza_countArr', array_unique($pizza_countArr));
        $this->set('wingsArr', $wingsArr);

        $this->render('refresh_flavours');
    }
    
     public function drink_details($id = null)
             {
        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => $id])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');


        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->order(['Addons.order' => asc])
                ->all();

        $this->set('addons_all', $addons_all);


        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $selection = $AddonCategories->find()
                ->where(['AddonCategories.selection' => "single"])
                ->all();
        $this->set('selection', $selection);


        $addons = $Addons->find()
                ->order(['Addons.order' => asc])
                ->all();
        $this->set('addons', $addons);
        $this->set('addon_catArr', $addon_catArr);
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Addons']
        ]);

        // echo $product['category_id'];exit;

         $Combos = TableRegistry::get('Combos');
            $party_detailsArr = $Combos->find()
                    ->where(['Combos.type' => 'drink', 'Combos.size' => $drinks_size])
                    ->order(['Combos.id' => asc])
                    ->all();
       
        foreach ($party_detailsArr as $key => $value) {
            $nameidArr[] = $value['name'];
        }

        $Addons = TableRegistry::get('Addons');
        $addonsArr = $Addons->find()
                ->where(['Addons.id IN' => $nameidArr])
                ->all();

        foreach ($addonsArr as $key => $value) {
            $wingsArr[] = array('id' => $value['id'], 'name' => $value['name']);
        }
        //print_r($wingsArr); exit;



        $this->set('product', $product);
        $this->set('_serialize', ['product']);

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['custom'][$key];
            //echo "<pre>";print_r($selected_items); exit;
            $this->set('selected_items', $selected_items);
            $this->set('session_key', $key);
            
            $drinks_size=$selected_items['size_id'];
            $Combos = TableRegistry::get('Combos');
            $party_detailsArr = $Combos->find()
                    ->where(['Combos.type' => 'drink', 'Combos.size' => $drinks_size])
                    ->order(['Combos.id' => asc])
                    ->all();
       
        foreach ($party_detailsArr as $key => $value) {
            $nameidArr[] = $value['name'];
        }

        $Addons = TableRegistry::get('Addons');
        $addonsArr = $Addons->find()
                ->where(['Addons.id IN' => $nameidArr])
                ->all();
        $wingsArr=  array();
        foreach ($addonsArr as $key => $value) {
            $wingsArr[] = array('id' => $value['id'], 'name' => $value['name']);
        }
        //print_r($wingsArr); exit;

       

//        unset($cart_items['pizza'][$key]);
//        $session->delete('Cartarray1');
//        $session = new Session();
//        $sessionData = $session->write('Cartarray1',$cart_items);
        }
         $this->set('wingsArr', $wingsArr);
    }
    
    public function drink_price() {
        

		$delType = $this->request->session()->read('Config.deltype');
		$delLoc  = $this->request->session()->read('Config.location');
		
		   
        $drinks_size = $_POST['drinks_size'];
        $drinks_type = $_POST['drinks_type'];

        $product_count = $_POST['product_count'];
		$can_count  = 0;
        
        $Combos = TableRegistry::get('Combos');
        
        foreach($drinks_type as $key=>$value)
        {
            $drinks_count=$_POST['drinks_count_'.$value];
			$can_count  += $drinks_count;
			
              $addon_price_details = $Combos->find()
                    ->where(['Combos.type' => 'drink', 'Combos.size' => $drinks_size, 'Combos.name' => $value,'Combos.restaurants_id' => $delLoc])
                    ->last();
            if (isset($addon_price_details['price'])) {
                $price=$addon_price_details['price'];
                $final_price=$drinks_count*$price;
                $total+= $final_price;

               
            }
        }
          
		// if($drinks_size==CAN_ID)
		// {
			
		// 	if($can_count>=4 && $can_count <6)
		// 	{   
				
		// 		$final_price   = 3.49;
		// 		$can_count     = $can_count - 4;
				
		// 		$addon_price_details['price'] = 3.49/4;
		// 		$addon_price_details['price'] = number_format((float) $addon_price_details['price'], 2, '.', '');
				
		// 		$price=$addon_price_details['price'];
		// 		$final_price += $can_count*$price;
		// 		$total = $final_price;
		// 	}
			
		// 	if($can_count>=6)
		// 	{   
		// 		$final_price   = 4.49;
		// 		$can_count     = $can_count - 6;
				
		// 		$addon_price_details['price'] = 4.49/6;
		// 		$addon_price_details['price'] = number_format((float) $addon_price_details['price'], 2, '.', '');
				
		// 		$price=$addon_price_details['price'];
		// 		$final_price += $can_count*$price;
		// 		$total = $final_price;
		// 	}
			
		// }
      
        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        echo $final;exit;
        // print_r($final) ; exit();
        //print_r($_POST); exit();
//        $ff = array('total' => $final, 'id' => $combo_id);
//        $ff = json_encode($ff);
//        echo $ff;
//        exit;
    }
    public function special_details($id = null) {
        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => $id])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');


        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->order(['Addons.order' => asc])
                ->all();

        $this->set('addons_all', $addons_all);


        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $selection = $AddonCategories->find()
                ->where(['AddonCategories.selection' => "single"])
                ->all();
        $this->set('selection', $selection);


        $addons = $Addons->find()
                ->order(['Addons.order' => asc])
                ->all();
        $this->set('addons', $addons);
        $this->set('addon_catArr', $addon_catArr);
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Addons']
        ]);

        // echo $product['category_id'];exit;

        if ((BASKET_ID == $product['category_id'])) {
            $addons_side = $Addons->find()
                    ->where(['Addons.addon_category_id ' => BSIDE_ID])
                    ->all();
            $this->set('addons_side', $addons_side);
        }


        $this->set('product', $product);
        $this->set('_serialize', ['product']);

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['custom'][$key];
            //echo "<pre>";print_r($selected_items); exit;
            $this->set('selected_items', $selected_items);
            $this->set('session_key', $key);

//        unset($cart_items['pizza'][$key]);
//        $session->delete('Cartarray1');
//        $session = new Session();
//        $sessionData = $session->write('Cartarray1',$cart_items);
        }
    }
    
     public function special_total_price() {
        // echo  $_POST['size'];exit;
        //print_r($_POST);exit();
         $sauce_count = $_POST['sauce_count'];
        $max_sauce_count = $_POST['max_sauce_count'];
        $maincategory_id = $_POST['maincategory_id'];

        $sizeCount      = 1;
        $sizeCounttotal = 0;
        $totalFlag      = True;

        $product_count = $_POST['product_count'];
        $product_id = $_POST['product_id'];
        //$total=$_POST['start_price'];
        $wings_type = "";
        if (isset($_POST['wings_type'])) {
            $wings_type = $_POST['wings_type'];
        }

        if (isset($_POST['salad_size'])) {
            $salad_size = $_POST['salad_size'];
        } else {
            $salad_size = "";
        }

        if (isset($_POST['psize'])) {
            $psize = $_POST['psize'];
        } else {
            $psize = "";
        }
         if (isset($_POST['bsize'])) {
            $bsize = $_POST['bsize'];
        } else {
            $bsize = "";
        }
        
        $total = $_POST['product_baseprice'];
        if (($maincategory_id == APPETIZER_ID) || ($maincategory_id == POUTINE_ID) || ($maincategory_id == WING_ID) || ($maincategory_id == SALAD_ID)|| ($maincategory_id == BASKET_ID)) {
            if (($wings_type == 'breaded') || ($salad_size == 'large') || ($psize == 'large')|| ($bsize == '5')) {
                $total = $_POST['product_largeprice'];
            } else {
                $total = $_POST['product_baseprice'];
            }
        }

        if ($maincategory_id == SALAD_ID) {
            if (isset($_POST['salad_size'])) {
                $green_price = "";
                $salad_size = $_POST['salad_size'];
                $salad_green_id = $_POST['salad_green_id'];
                $green_price = $_POST['salad_green'][$salad_green_id][$salad_size];
                $total+=$green_price;
            }
        }
        if ($product_id == ITALIAN_ALFREDO_ID) {
            $italian_alfredo_type = $_POST['italian_alfredo_type'];
            $italian_alfredo_type_price = $_POST['italian_alfredo_type_price_' . $italian_alfredo_type];
            $total = $italian_alfredo_type_price;
        }

        if ($product_id == ITALIAN_SPAGHETTI_ID) {
            $italian_spaghetti_type = $_POST['italian_spaghetti_type'];
            $italian_spaghetti_type_price = $_POST['italian_spaghetti_type_price_' . $italian_spaghetti_type];
            $total = $italian_spaghetti_type_price;
        }

        if ($maincategory_id == DRINK_ID) {
            if (isset($_POST['drinks_size'])) {
                $drinks_size = $_POST['drinks_size'];
                $total = $_POST['drinks_size_' . $drinks_size];
            } else {
                $drinks_size = "";
            }
        }

        if ($maincategory_id == DESSERT_ID) {
            if (isset($_POST['desert_type'])) {
                $desert_type = $_POST['desert_type'];
                $desert_type1 = str_replace(' ', '', $desert_type);
                $total+=$_POST['desert_type_' . $desert_type1];
            } else {
                $drinks_size = "";
            }
        }

        if ($product_id == APPETIZER_STRIPS_ID) {

            $apetizer_type = $_POST['apetizer_type'];
            if (in_array('CHEESE', $apetizer_type)) {
                if (in_array('BACON', $apetizer_type)) {
                    $total+=3;
                } else {
                    $total+=2;
                }
            } else if (in_array('BACON', $apetizer_type)) {
                $total+=2;
            }
            //print_r($apetizer_type);exit;
        }
//echo $total;exit;
        $i = 1;
        $Prices = TableRegistry::get('Prices');

        if (isset($_POST['addons'])) {
            foreach ($_POST['addons'] as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    if (isset($value1['addon_id'])) {

                            /*** for counting sauce after no. of defaults***/

                            $totsize     = $_POST['drinks_count'][$value1['addon_id']];
	                    if($totsize!='' && $totsize!=0):
	                       $sizeCount = $totsize;
	                    endif;
                            $sizeCounttotal += $sizeCount;
                          
                            if($sizeCounttotal>$max_sauce_count && $totalFlag == true)
                             {
                                 $sizeCount = $sizeCounttotal-$max_sauce_count;
                                 $totalFlag = false;
                             }
                              
                            /**********************/
                        
                         if (($sizeCounttotal > $max_sauce_count)&&($value1['addon_id'] != NOSIDE_ID)&&($value1['addon_id'] != POUTINEADDON_ID)) {

                        if (($maincategory_id == POUTINE_ID) && ($psize == 'large'))
                            {
                            $addon_price = $value1['lprice'];
                        } else {

                            
                            $addon_price = $value1['price'] * $sizeCount;
                             //$addon_price = $value1['price'] ;
                           
                        }
                        $total+=$addon_price;
                        
                         }
                         $i++;
                        
                        if ($value1['addon_id'] == NOSIDE_ID) {
                            $total-=NOSIDE_PRICE;
                        } 
                        if ($value1['addon_id'] == POUTINEADDON_ID) {
                            $total+=$value1['price'];
                        }
                        
                        
                    }
                }
            }
        }






        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        print_r($final);
        exit();

        //print_r($_POST); exit();
    }
    public  function addon_price()
    {      
        // print_r($_POST);exit;
         $tagcattype=$_POST['tagcattype'];
         $tagid=$_POST['tagid'];
         $tagcatid=$_POST['tagcatid'];
         $side=$_POST['addon'][$tagcatid][$tagid][side];
         $type=$_POST['addon'][$tagcatid][$tagid][type];
         $size = $_POST['size'];
         $product_id = $_POST['product_id'];
         if(isset($_POST['pizza1_size']))
         {
             $size = $_POST['pizza1_size'];
         }
           $Prices = TableRegistry::get('Prices'); 

         if($tagcattype=="top")
         {
                    if ($side == "left" || $side == "right") {
                        $side1 = "full";
                    } else {
                        $side1 = "full";
                    }

                    $addon_id = $tagid;
                    $addon_price_details = $Prices->find()
                            ->where(['Prices.product_id' => $product_id, 'Prices.side' => $side1, 'Prices.type' => $type, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                            ->first();
                   $price= $addon_price_details['price'] ;
                   $final=number_format((float)$price, 2, '.', '');
                   echo $final; exit;
               }
               else
               {



                    $addon_id = $tagid;
                    $addon_dip_price_details = $Prices->find()
                        ->where(['Prices.product_id' => $product_id, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                        ->first();
                    $addon_dip_price = $addon_dip_price_details['price'];
                    $final=number_format((float)$addon_dip_price, 2, '.', '');
                    echo $final; exit;

                 }
    }
    public function party_view() {
        $tab_index = $_POST['tab_index'];
        $this->set('tab_index', $tab_index);

        $this->layout = false;
        
        $type=$_POST['type'];
        $Combos = TableRegistry::get('Combos');
        $party_details = $Combos->find()
                ->where(['Combos.type' => $type])
                ->order(['Combos.ordering' => asc])
                ->all();
 foreach ($party_details as $key => $value) {
                    $party_detailsArr[]=array(
                        'id'=>$value['id'],
                        'pizza_count'=>$value['pizza_count'],
                        'size'=>$value['size'],
                        'wings'=>$value['wings'],
                        'count'=>$value['count'],
                        'wings_price'=>$value['wings_price'],
                        'price'=>$value['price'],
                        'name'=>$value['name'],
                        'picture'=>$value['image'],
                        );
                }
        $this->set('party_details', $party_detailsArr);
  // print_r($party_detailsArr);exit;

       
        $this->render('party_view');
        //print_r($subcatArr);
        // exit();
    }
    public function final_combo_cart($value='')
    {
        /*$type=$_POST['type'];
        $session_edit_key=$_POST['session_edit_key'];
        $session = $this->request->session();

        if($type=="edit")
        {
            $cart_items=$session->read('Cartarray1');
            unset($cart_items['combo'][$session_edit_key]);
            array_push($cart_items['combo'],$cart_items['demo'][$session_edit_key]);
            $sessionData = $session->write('Cartarray1', $cart_items);
            echo "updated";exit;
            
        }
        else
        {
            $cart_items=$session->read('Cartarray1');
            array_push($cart_items['combo'],$cart_items['demo'][0]);
            $sessionData = $session->write('Cartarray1',$cart_items);
            echo "added";exit;
        }*/
		
		$session = $this->request->session();
        $cart_items=$session->read('Cartarray1');
      
        array_push($cart_items['combo'],$cart_items['demo'][0]);
        $sessionData = $session->write('Cartarray1',$cart_items);

        $cart_items1=$session->read('Cartarray1');
      
        echo "sucess";exit;
       
      

    }

    /*public function refreshcomboprice()
    {
        $pizza_selection=$_POST['pizza_selection'];
        $combo_start_price=$_POST['combo_start_price'];
        $session_key=$_POST['session_key'];
        $product_combo_id=$_POST['product_combo_id'];
        $session = $this->request->session();
        $cart_items=$session->read('Cartarray1');
        $dec=1;
        if($pizza_selection==1)
        {
            $index=$pizza_selection-$dec;
        }
        else
        {
            $index=$pizza_selection+$dec;
        }
        
        // echo $session_key." ".$product_combo_id." pizza".$index." total_price";exit;
        $finalstart_price=$combo_start_price+$cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index]['total_price'];


        echo $finalstart_price;exit;

    }*/

    /*public function refreshcombocart()
    {
        
        $size=$_POST['size'];
        $product_id=$_POST['product_id'];

        $pizza_selection=$_POST['pizza_selection'];
        $combo_start_price=$_POST['combo_start_price'];
        $session_key=$_POST['session_key'];
        $product_combo_id=$_POST['product_combo_id'];
        $session = $this->request->session();
        $cart_items=$session->read('Cartarray1');
        

        $dec=1;
        $total=0;
        $total_pizza_price=0;
        if($pizza_selection==1)
        {
            $index=$pizza_selection-$dec;
        }
        else
        {
            $index=$pizza_selection+$dec;
        }
        if(!isset($cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index]['toppings']))
        {
            echo "no change";exit;
        }

         // print_r($cart_items);exit;


        $Prices = TableRegistry::get('Prices');

        $var=$cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index]['toppings'];
        $i=0;
        
        foreach ($var as $key => $value) 
        {

            $addon_cat=$value['addon_cat'];
            $k=0;
            foreach ($value['addon_subcat'] as $skey => $svalue) 
            {
                $side=$svalue[addonnames]['side'];
                $type=$svalue[addonnames]['type'];
                $addon_id=$svalue[addonnames]['id'];
                $side=$svalue[addonnames]['side'];
                if ($side == "left" || $side == "right")
                 {
                        $side1 = "half";
                  } else 
                  {
                        $side1 = "full";
                   }
                    $addon_price_details = $Prices->find()
                            ->where(['Prices.product_id' => $product_id, 'Prices.side' => $side1, 'Prices.type' => $type, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                            ->first();
                    if (isset($addon_price_details['price']))
                     {
                        $addon_price = $addon_price_details['price'];
                        $total+=$addon_price;
                        $cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index]['toppings'][$i][addon_subcat][$k][addonnames][price]=$addon_price;

                     }
            $k++;
            }
        $i++;
        }

        $cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index]['total_price']=$total;
        $start_price=$cart_items['demo'][$session_key][$product_combo_id]['combo_details']['start_price'];
        foreach ($cart_items['demo'][$session_key][$product_combo_id]['pizza'] as $key => $value) 
        {
            $total_pizza_price+=$value[total_price];
        }

        $final=$total_pizza_price+$start_price;
        $cart_items['demo'][$session_key][$product_combo_id]['combo_details']['final_price']=$final;
        $cart_items['demo'][$session_key][$product_combo_id]['combo_details']['size']=$size;
        $cart_items['demo'][$session_key][$product_combo_id]['pizza'][0][size]=$size;
        $cart_items['demo'][$session_key][$product_combo_id]['pizza'][1][size]=$size;


        $sessionData = $session->write('Cartarray1',$cart_items);
        $session = $this->request->session();
        $cart_items=$session->read('Cartarray1');
        print_r($cart_items);exit;
    }*/
    public function dippingsauce_details($id='')
    {
        $delLoc  = $this->request->session()->read('Config.location');
        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => $id])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');


        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->order(['Addons.order' => asc])
                ->all();

        $this->set('addons_all', $addons_all);


        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }

        $selection = $AddonCategories->find()
                ->where(['AddonCategories.selection' => "single"])
                ->all();
        $this->set('selection', $selection);


        $addons = $Addons->find()
                ->order(['Addons.order' => asc])
                ->all();
        $this->set('addons', $addons);
        $this->set('addon_catArr', $addon_catArr);
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Addons']
        ]);

        // echo $product['category_id'];exit;

         $Combos = TableRegistry::get('Combos');
            $party_detailsArr = $Combos->find()
                    ->where(['Combos.type' => 'drink', 'Combos.size' => $drinks_size])
                    ->order(['Combos.id' => asc])
                    ->all();
       
        foreach ($party_detailsArr as $key => $value) {
            $nameidArr[] = $value['name'];
        }

        $Addons = TableRegistry::get('Addons');

        if($delLoc == OTTAWA_ID)
        {
         $addonsArr = $Addons->find()
                ->where(['Addons.main_category_id ' =>DIPPINGSPRODUCTCATEGORY_ID,'id <>'=>WING_NO_SAUCE_ID,'Addons.id NOT IN' =>unserialize(OTTAWA_REMOVE_DIP)])
                ->order(['Addons.order'=> 'asc'])
                ->all();
        }
        else
        {
         $addonsArr = $Addons->find()
                ->where(['Addons.main_category_id ' =>DIPPINGSPRODUCTCATEGORY_ID,'id <>'=>WING_NO_SAUCE_ID])
                ->order(['Addons.order'=> 'asc'])
                ->all();   
        }

        foreach ($addonsArr as $key => $value) {
            if($value['id']!=453):
            $dipsArr[] = array('id' => $value['id'], 'name' => $value['name'],'price' => $value['price'],'description'=>$value['description']);
	    endif;
        }
        //print_r($wingsArr); exit;



        $this->set('product', $product);
        $this->set('_serialize', ['product']);

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['custom'][$key];
            //echo "<pre>";print_r($selected_items); exit;
            $this->set('selected_items', $selected_items);
            $this->set('session_key', $key);
            
            $drinks_size=$selected_items['size_id'];
            $Combos = TableRegistry::get('Combos');
            $party_detailsArr = $Combos->find()
                    ->where(['Combos.type' => 'drink', 'Combos.size' => $drinks_size])
                    ->order(['Combos.id' => asc])
                    ->all();
       
        foreach ($party_detailsArr as $key => $value) {
            $nameidArr[] = $value['name'];
        }

        $Addons = TableRegistry::get('Addons');
        $addonsArr = $Addons->find()
                ->where(['Addons.main_category_id ' =>DIPPINGSAUCEADDONCATEGORY_ID])
                ->all();
        $dipsArr=  array();
        foreach ($addonsArr as $key => $value) {
            $dipsArr[] = array('id' => $value['id'], 'name' => $value['name'], 'price' => $value['price']);
        }
        // print_r($wingsArr); exit;

       

//        unset($cart_items['pizza'][$key]);
//        $session->delete('Cartarray1');
//        $session = new Session();
//        $sessionData = $session->write('Cartarray1',$cart_items);
        }
         $this->set('dipsArr', $dipsArr);
    
    }

    public function dipping_price()
    {
        $delLoc  = $this->request->session()->read('Config.location'); 
        $dips_type = $_POST['dips_type'];
        $product_count = $_POST['product_count'];
        foreach($dips_type as $key=>$value)
        {
            $drinks_count=$_POST['drinks_count_'.$value];
            $price=$_POST['drinks_price_'.$value];
            $final_price=$drinks_count*$price;
            if($value == CREST_CHILLI_ID)
            {
                
               $total+= $final_price-0.35; 
           

            }
            else
            {
            if($delLoc == OTTAWA_ID)
            {
               $total+= $final_price+OTTAWA_DIP_DIFFRENCE;
            }
            else if($delLoc == DANFORTH_ID)
            {
                $total+= $final_price+DANFORTH_DIP_DIFFRENCE;
            }
            else
            {
               $total+= $final_price; 
            }

            }
            
            

        }
        $final = $total * $product_count;
        $final = number_format((float) $final, 2, '.', '');
        echo $final;exit;
    }
	
	
	/**** reginos pizza specials  ****/
	
	/**
	 *@csp Jul-05
	 *for appatizer configuraion
	 *return configuration details
	**/
	
	public function appatizer_details($id = null) {
        $delLoc  = 0;
        $delType = '';
        
        $delType = $this->request->session()->read('Config.deltype');
        $delLoc  = $this->request->session()->read('Config.location');
        
        
        
        $Sizes = TableRegistry::get('Sizes');
        $pizza_size_prices = $Sizes->find()
                ->where(['Sizes.product_id' => $id,'Sizes.type'=>$delType])
                ->all();
    
        $product = $this->Products->get($id, ['contain' => ['Categories', 'Addons']]);

        $PizzaSizes = TableRegistry::get('PizzaSizes');
        $ps         = $PizzaSizes->find()->all();
        foreach($ps as $sz)
        {
            $ps_arr[$sz->size_value] = $sz->size_label;
        }
        
        
        $AddonCategories   = TableRegistry::get('AddonCategories');
        $Prices            = TableRegistry::get('Prices');
        $addon_type_prices = $Prices->find("all")->where(['Prices.product_id' => id])->all();

        $Addons = TableRegistry::get('Addons');
        $addonCategories = $AddonCategories->find("all")->order(['AddonCategories.order' => asc]);
        $addon_maincat = $AddonCategories->find()->where(['AddonCategories.parent_id' => 0])->all();

        $query = $Addons->find()->order(['Addons.order' => 'asc'])->all();
        

        // echo '<pre>';print_r($addon_type_prices); exit();

        $addon_dips = $AddonCategories->find()
                ->where(['AddonCategories.id' => DIPS_ID])
                ->first();

        $addon_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => TOPPINGS_ID])
                ->first();

        $addon_free_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => FREE_TOPPINGS_ID])
                ->first();

        $addon_special = $AddonCategories->find()
                ->where(['AddonCategories.id' => SPECIAL_INSTRUCTION_ID])
                ->first();

      

        $this->set('addon_dips', $addon_dips);
        $this->set('addon_toppings', $addon_toppings);
        $this->set('addon_free_toppings', $addon_free_toppings);
        $this->set('addon_special', $addon_special);
        // $this->set('size', $size);



        $this->set('addon_type_prices', $addon_type_prices);
        $this->set('ps_arr', $ps_arr);
        $this->set('addon_maincat', $addon_maincat);
        $this->set('addonCategories', $addonCategories);
        $this->set('addons', $query);
        $this->set('product', $product);
        $this->set('pizza_size_prices', $pizza_size_prices);
        $this->set('_serialize', ['product']);

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['pizza'][$key];
            $this->set('selected_items', $selected_items);
            $this->set('session_key', $key);


        }
    }
	
	
	/**
	 *@csp Jul-09
	 *for calculate addon price for pizza configuraion
	 *return configuration details
	**/
	public function pizza_calculate() {
		
        $product_count  = $_POST['product_count'];
        $product_id 	= $_POST['product_id'];
        $isCheck        = false;
		$initial_total  = $total = $_POST['start_price'];

        $size 			= $_POST['size'];
        $Prices 		= TableRegistry::get('Prices');
		$Products 		= TableRegistry::get('Products');
		$ToppingPrice 	= TableRegistry::get('ToppingPrice');
		$toppingsArr    = $ToppingPrice->find();	

        $toppings       = array();
        foreach($toppingsArr as $key => $value)
			{
				$toppings[$value->size][$value->type] = $value->price;
			}
 		

               
		$maxCount       = 0;
		$uptotops       = 0;
		$increment      = 0;
		
		$defaulttopps   = $Products->find()->where(['id' => $product_id ])->first();	
		$defaultTopscnt = explode(',',$defaulttopps->default_toppings);
		$defdips        = explode(',',$defaulttopps->default_dips);
		if($defaulttopps->category_id == DAY_SPECIAL)
		{
			$maxCount = DAY_SPECIAL_TOPPS;
		}
		elseif($defaulttopps->category_id == PICKUP_AND_WALKIN)
		{
			$maxCount = $defaulttopps->default_count; 
		}
		else
		{
			if($defaulttopps->default_count!='')
			{
				$maxCount       = $defaulttopps->default_count;
			}
			else
			{
				$maxCount       = count($defaultTopscnt);
			}
		}
		

		$firstarray = unserialize(DOUBLE_ID);
		$trippleID  = TRIPPLE_ID;
		
		
		$defaultsetPrice = $toppings[$size]['full'];

		$setPrice        = $defaultsetPrice*$maxCount;

		$premimumprice 	 = 0;

        foreach ($_POST['addon'] as $key => $main) 
		{
            foreach ($main as $key1 => $subcat) 
			{
                if ($_POST['product_id'])
				{
                    $side = $subcat['side'];
                    if ($side == "left" || $side == "right") 
					{
                        $side1 = "half";
                    } else 
					{
                        $side1 = "full";
                    }

                    $type 	  = $subcat['type'];
					
					if($type!='')
					{ 
						$addon_id = $subcat['addon_id'];
						
						$increment = $this->get_increment($side1,$type);
						$uptotops += $increment;
						$tcount    = $type[0];						
						
                                                if($addon_id !=CRUST_CHILLY)
						{
							$addon_price_details['price'] = $toppings[$size][$side1] ;
						}
						else
						{
							$addon_price_details['price'] = 0 ;
						}		
						
						
						
						
						if (isset($addon_price_details['price'])) 
						{
							if(in_array($addon_id,$firstarray))
							{
								$premium = 2;
							}
							elseif($addon_id==$trippleID)
							{
								$premium = 3;
							}
							else
							{
								$premium = 1;
							}
							$addon_price = $addon_price_details['price']*$tcount*$premium ; 
							$total+=$addon_price;
							$isCheck  = true;
														
						}
					}
                    
                }
            }
        }

		//echo $premimumprice .'|';
        $checktotal = $total-$setPrice ;
        if($checktotal<=$initial_total)
         {
		$total = $initial_total ; 
         }
	else
        {
		$total = $checktotal ; 
        }

        if (isset($_POST['dips'])) {
            foreach ($_POST['dips'] as $key => $value) {
                
                 /** dips[][addon_id] changd to dips[][addon_id][]  in speciality pizza **/

                  if(isset($_POST['product_combo_id']) && $_POST['product_combo_id'] !=''):
                       $addon_id = $value['addon_id'];
                       $_POST['tagcatid']=SAUCEID;
                  else:
                       $addon_id = $value['addon_id'][0]; 
                  endif;
                 

                /*$addon_dip_price_details = $Prices->find()
                        ->where(['Prices.product_id' => $product_id, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                        ->first();*/
                $addon_dip_price_details['price'] = '';

                if($key != DOUGH_ID)
		          {
                     
                     if(isset($value['addon_id'][0]) && $value['addon_id'][0] != PIZZA_SAUCE_ID && $value['addon_id'][0] != NO_SAUCE_ID && !in_array($value['addon_id'][0],$defdips))
			             {
				            $addon_dip_price_details['price'] = $defaultsetPrice ; 
			             }
		          }
                else
                  {
			         if($value['addon_id'][0] == GLUTEN_ID)
			         {
				    $addon_dip_price_details['price'] = GLUTEN_PRICE ; 
			}
                  }



                if (isset($addon_dip_price_details['price'])) {
                    $addon_dip_price = $addon_dip_price_details['price'];
                    $total+=$addon_dip_price;
                }
       
            }
           
        }

        if (isset($_POST['special']['addon_id'])) {

            $addon_id = $_POST['special']['addon_id'];
            $addon_spl_price_details = $Prices->find()
                    ->where(['Prices.product_id' => $product_id, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                    ->first();
            if (isset($addon_spl_price_details['price'])) {
                $addon_spl_price = $addon_spl_price_details['price'];
                //echo $addon_price; exit();


 
                $total+=$addon_spl_price;
            }
        }

        if (isset($_POST['crust'])) {
            $price = CRUST_PRICE;
            $crust = $_POST['crust'];
            if ($crust == "thick") {
                $total+=$price;
            }
        }

        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        echo($final);
        exit();

     
    }
	
	
	/**
	 *@csp Jul-09
	 *for calculate addon price for pizza configuraion
	 *return configuration details
	**/
	public function special_pizza_calculate() {
		
        $product_count  = $_POST['product_count'];
        $product_id 	= $_POST['product_id'];
        $isCheck        = false;
		$initial_total  = $total = $_POST['start_price'];

        $size 			= $_POST['size'];
        $Prices 		= TableRegistry::get('Prices');
		$Products 		= TableRegistry::get('Products');
		$ToppingPrice 	= TableRegistry::get('ToppingPrice');
		$toppingsArr    = $ToppingPrice->find();	

        $toppings       = array();
        foreach($toppingsArr as $key => $value)
			{
				$toppings[$value->size][$value->type] = $value->price;
			}
 		

               
		$maxCount       = 0;
		$uptotops       = 0;
		$increment      = 0;
		
		$defaulttopps   = $Products->find()->where(['id' => $product_id ])->first();	
		$defaultTopscnt = explode(',',$defaulttopps->default_toppings);
		$defdips        = explode(',',$defaulttopps->default_dips);
		if($defaulttopps->category_id == DAY_SPECIAL)
		{
			$maxCount = DAY_SPECIAL_TOPPS;
		}
		else
		{
			if($defaulttopps->default_count!='')
			{
				$maxCount       = $defaulttopps->default_count;
			}
			else
			{
				$maxCount       = count($defaultTopscnt);
			}
		}
		

		$firstarray = unserialize(DOUBLE_ID);
		$trippleID  = TRIPPLE_ID;
		
		
		$defaultsetPrice = $toppings[$size]['full'];

		$setPrice        = $defaultsetPrice*$maxCount;

		$premimumprice 	 = 0;

        foreach ($_POST['addon'] as $key => $main) 
		{
            foreach ($main as $key1 => $subcat) 
			{
                if ($_POST['product_id'])
				{
                    $side = $subcat['side'];
                    if ($side == "left" || $side == "right") 
					{
                        $side1 = "full";
                    } else 
					{
                        $side1 = "full";
                    }

                    $type 	  = $subcat['type'];
					
					if($subcat['default'] == 0)
					{ 
						$addon_id  = $subcat['addon_id'];						
						$tcount    = $type[0];	
					
                        if($addon_id!=CRUST_CHILLY)
						{
							$addon_price_details['price'] = $toppings[$size][$side1] ;
						}						
						else
						{
							$addon_price_details['price'] = 0 ;
						}

						if (isset($addon_price_details['price'])) 
						{
							if(in_array($addon_id,$firstarray))
							{
								$premium = 2;
							}
							elseif($addon_id==$trippleID)
							{
								$premium = 3;
							}
							else
							{
								$premium = 1;
							}
							$addon_price = $addon_price_details['price']*$tcount*$premium ; 
							$total+=$addon_price;
														
						}
					}
                    
                }
            }
        }

        if (isset($_POST['dips'])) {
            foreach ($_POST['dips'] as $key => $value) {
                
                 /** dips[][addon_id] changd to dips[][addon_id][]  in speciality pizza **/

                  if(isset($_POST['product_combo_id']) && $_POST['product_combo_id'] !=''):
                       $addon_id = $value['addon_id'];
                       $_POST['tagcatid']=SAUCEID;
                  else:
                       $addon_id = $value['addon_id'][0]; 
                  endif;
                 

                /*$addon_dip_price_details = $Prices->find()
                        ->where(['Prices.product_id' => $product_id, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                        ->first();*/
                $addon_dip_price_details['price'] = '';

                if($key != DOUGH_ID)
		  {
                     
                     if(isset($value['addon_id'][0]) && $value['addon_id'][0] != PIZZA_SAUCE_ID && $value['addon_id'][0] != NO_SAUCE_ID && !in_array($value['addon_id'][0],$defdips))
			{
				$addon_dip_price_details['price'] = $defaultsetPrice ; 
			}
		  }
                else
                  {
			if($value['addon_id'][0] == GLUTEN_ID)
			{
				$addon_dip_price_details['price'] = GLUTEN_PRICE ; 
			}
                  }



                if (isset($addon_dip_price_details['price'])) {
                    $addon_dip_price = $addon_dip_price_details['price'];
                    $total+=$addon_dip_price;
                }
       
            }
           
        }

        if (isset($_POST['special']['addon_id'])) {

            $addon_id = $_POST['special']['addon_id'];
            $addon_spl_price_details = $Prices->find()
                    ->where(['Prices.product_id' => $product_id, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                    ->first();
            if (isset($addon_spl_price_details['price'])) {
                $addon_spl_price = $addon_spl_price_details['price'];
                //echo $addon_price; exit();


 
                $total+=$addon_spl_price;
            }
        }

        if (isset($_POST['crust'])) {
            $price = CRUST_PRICE;
            $crust = $_POST['crust'];
            if ($crust == "thick") {
                $total+=$price;
            }
        }

        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        echo($final);
        exit();

     
    }

	/**
	 *@csp Jul-09
	 *for calculate the total number of addons
	 *return configuration details
	**/
    public function get_increment($side ,$type) 
	{
        if($side=='half')
		{
			$inc = 0.5;
		}
		else
		{
			$inc = 1.0;
		}
	 if($type!='')
		{
			$inc = $inc*$type;
		}
		return $inc;
    }
	
	
	 public function combo_products_view()
    {
		
		$delLoc  = 0;
		$delType = '';
		
		$delType = $this->request->session()->read('Config.deltype');
		$delLoc  = $this->request->session()->read('Config.location');
		
		date_default_timezone_set("Canada/Eastern");
		 $date_no   = date ('w');
		 $days_arr  = array(0, $date_no);
		
		
        $tab_index=$_POST['tab_index'];
        $this->layout=false;
        $subcatArr=array();
        $productsArr=array();
        $maincategory_id=  $_POST['maincategory_id'];
              $cat=array(PIZZA_COMBO_ID);
             $this->set('productsArr', $productsArr); 
             $this->set('tab_index', $tab_index);     

              $ComboCategories= TableRegistry::get('ComboCategories');
              $combocategories = $ComboCategories->find("all")
              ->where(['ComboCategories.id' =>DAY_COMBO_ID])
                ->all();
                foreach ($combocategories as $key => $value)
                 {
                  $pizza_combo_name=$value['name'];
                  $pizza_combo_id=$value['id'];
                 }
                			  
         
            $Combo= TableRegistry::get('Combo');


             if($maincategory_id==GAMECATEGORY_ID)
			 {
				 $pizza_combo_name='Manager Special';
                 $pizza_combo_id=0;
				 
				 $pizza_combo = $Combo->find()->where(['Combo.type'=>'manager','Combo.restaurent_id'=>$delLoc])->all();
			 }
			 elseif($maincategory_id==ONLINE_SPECIAL_ID)
			 {
				 $pizza_combo_name='Online Special';
                 $pizza_combo_id=0;
				 
				 $pizza_combo = $Combo->find()->where(['Combo.type'=>'online','Combo.restaurent_id'=>$delLoc,$delType=>1,'day_no IN'=>$days_arr])->order(['Combo.ordering' => asc])->all();
			 }
			 elseif($maincategory_id==DELIVERY_SPECIAL_ID)
			 {
				 $pizza_combo_name='Delivery Special';
                 $pizza_combo_id=0;
				 
				 $pizza_combo = $Combo->find()->where(['Combo.type'=>'delivery_spec','Combo.restaurent_id'=>$delLoc,$delType=>1])->all();
			 }
			 else
			 {
				 $pizza_combo = $Combo->find()->where(['Combo.type'=>'party','Combo.restaurent_id'=>$delLoc])->order(['Combo.ordering' => asc])->all();
			 }
             
                
                foreach ($pizza_combo as $key => $value) 
                {
                  $pizza_comboArr[]=array('id'=>$value['id'],
                    'name'=>$value['name'],
                    'image'=>$value['image'],
                    'price'=>$value['price'],
                    'desc'=>$value['description'],
                    'small_price' =>$value['small_price'],
                    'large_price'=>$value['large_price'],
                    'medium_price'=>$value['medium_price'],
                    'xlarge_price'=>$value['xlarge_price'],
                    'mega_price'=>$value['mega_price'],
                    'paty_price'=>$value['paty_price'],
                    'small'=>$value['small'],
                    'large'=>$value['large'],
                    'medium'=>$value['medium'],
                    'xlarge'=>$value['xlarge'],
                    'mega'=>$value['mega'],
                    'party'=>$value['party'],



              );
                }
				

                 $this->set('pizza_comboArr', $pizza_comboArr);      
               $ComboProducts= TableRegistry::get('ComboProducts');
              $pizza_combo_products = $ComboProducts->find("all");
              foreach ($pizza_combo_products as $key => $value) 
              {
                $pizza_combo_productsArr[$value['combo_id']][]=array('combo_id'=>$value['combo_id']);
              }
              $this->set('pizza_combo_productsArr', $pizza_combo_productsArr);      
            
			/*** for saucy N side dish  ***/

			$Products= TableRegistry::get('Products');
			$products = $Products->find("all")->where(['Products.category_id' =>GAMECATEGORY_ID,'Products.restaurants_id'=>$delLoc])->all();
            foreach ($products as $key => $value) {
                $productsArr[GAMECATEGORY_ID] = array('id' => $value['id'], 'name' => $value['name'], 'description' => $value['description'],
                    'base_price' => $value['base_price'], 'image' => $value['image'], 'customize' => $value['customize'], 'large_price' => $value['large_price']);
            }
			$this->set('productsArr', $productsArr);      
			$this->set('pizza_combo_name', $pizza_combo_name);      
			$this->set('pizza_combo_id', $pizza_combo_id);
			$this->set('maincategory_id', $maincategory_id);	
			
			
            $this->render('combo_products_view');
           //print_r($subcatArr);
           // exit();


     }
	 
 	public function party_details($id = null,$productType = null, $p2 = null)
     { 
        


        $delLoc  = 0;
	    $delType = '';
	
	   $delType = $this->request->session()->read('Config.deltype');
	   $delLoc  = $this->request->session()->read('Config.location');
	


        $add_edit_check="add";
        if($_GET['combo']=="party")
        {
            $key=$_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
           
            //array_push($cart_items['demo'],$cart_items['combo'][$key]);
            $cart_items['demo'][$key]=$cart_items['combo'][$key];
            $sessionData = $session->write('Cartarray1',$cart_items);
            $add_edit_check="edit";
        }

        if($_GET['com']=="party")
        {
            $key=$_GET['key'];
            $add_edit_check="edit";
            $session_edit_key=$key;
        }

        $this->set('add_edit_check', $add_edit_check);
        $this->set('session_edit_key', $session_edit_key);

        $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => COMBO_WING_ID])
                ->all();
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');


       /* $addons_all = $Addons->find()
                ->where(['Addons.addon_category_id' =>DIPPINGSAUCEADDONCATEGORY_ID])
                ->order(['Addons.order' => asc])
                ->all();*/

        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->order(['Addons.order' => asc])
                ->all();
				

        $this->set('addons_all', $addons_all);

        $addon_catArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
        }
        $this->set('addon_catArr', $addon_catArr);
        $this->set('id', $id);


        $Sizes = TableRegistry::get('Sizes');
        $Products = TableRegistry::get('Products');
        $ProductsAll = $Products->find("all");
        foreach ($ProductsAll as $key => $value) {
            $ProductsArr[$value['id']] = $value['name'];
        }
        $Combo = TableRegistry::get('Combo');
        $product = $Combo->find("all")
                ->where(['Combo.id' => $id])
                ->all();

        $Combos = TableRegistry::get('Combos'); 
        $comboItem  = $Combos->find()->where(['Combos.id' => $id])->all()->toArray();

        $flavour          = TableRegistry::get('Combos');
        $flv_detailsArr = $flavour->find()->where(['Combos.type' => 'drink', 'Combos.size' => $comboItem[0]['pop_size'],'restaurants_id'=>$delLoc])->all();
       
        foreach ($flv_detailsArr as $key => $value) {
            $nameidArr[] = $value['name'];
        }
 
       $flavour_all = $Addons->find()->where(['Addons.id IN' =>$nameidArr])->order(['Addons.order' => asc])->all();
       $this->set('flavour_all', $flavour_all);

    
        $combo_category=COMBOPARTYID;
        $this->set('combo_category', $combo_category);


        $combo_in_sessioncheck = 0;
        $check_com = 0;
        $direct_check = 0;
        $combo_current_price = "";
        $combo_start_price = "";
        $combo_current_pizza_price = "";

        $session = $this->request->session();
        $cart_items = $session->read('Cartarray1');

        if (!isset($_GET['key']) && (!empty($cart_items['demo']))) {
            end($cart_items['demo']);
            $last_id = key($cart_items['demo']);
            if ($cart_items['demo'][$last_id]['combo_product_id'] == $id) {
                $check_com = 1;
                $combo_in_sessioncheck = 1;
                $combo_current_price = $cart_items['demo'][$last_id][$id][combo_details][final_price];
                $combo_start_price = $cart_items['demo'][$last_id][$id][combo_details][start_price];
                $combo_size = $cart_items['demo'][$last_id][$id][combo_details][size];

                if (isset($p2)) {
                   $index=$p2-1; 
                   
                $combo_current_pizza_price = $cart_items['demo'][$last_id][$id][$productType][$index][total_price];
//echo '<pre>';print_r($cart_items['demo']);
                }
            }
        }

 
       
        if (isset($_GET['key'])) {
            $check_com = 1;
            $combo_in_sessioncheck = 1;
        }


        $pizza_twin = 0;
        $pizza_id = '';
        $pizza_name = '';

        $pizza_selection = "";
        if (isset($p2)) 
         {

            if ($check_com == 1 || $check_com ==0 ) 
            {
                $direct_check = 1;
            }
            $pizza_selection = $p2-1;
            $pizza_twin = $p2;
            $pizza_name = $productType." ".$p2;
            $pizza_id = "";
        }
        $this->set('combo_in_sessioncheck', $combo_in_sessioncheck);
        $this->set('direct_check', $direct_check);


        $this->set('pizza_name', $pizza_name);
        $this->set('pizza_id', $pizza_id);
        $this->set('pizza_selection', $pizza_selection);
        $this->set('productType', $productType);
        $this->set('pizza_twin', $pizza_twin);

        //echo '<pre>';print_r($pizza1); exit();
        $AddonCategories = TableRegistry::get('AddonCategories');
        $Prices = TableRegistry::get('Prices');
        $addon_type_prices = $Prices->find("all")
                ->where(['Prices.product_id' => id])
                ->all();


        $Addons = TableRegistry::get('Addons');

        $addonCategories = $AddonCategories->find("all")
                ->order(['AddonCategories.order' => asc]);

        $addon_maincat = $AddonCategories->find()
                ->where(['AddonCategories.parent_id' => 0])
                ->all();

        $query = $Addons->find()
                ->order(['Addons.order' => asc])
                ->all();
        // echo '<pre>';print_r($addon_type_prices); exit();

        $addon_dips = $AddonCategories->find()
                ->where(['AddonCategories.id' => DIPS_ID])
                ->first();

        $addon_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => TOPPINGS_ID])
                ->first();

        $addon_free_toppings = $AddonCategories->find()
                ->where(['AddonCategories.id' => FREE_TOPPINGS_ID])
                ->first();

        $addon_special = $AddonCategories->find()
                ->where(['AddonCategories.id' => SPECIAL_INSTRUCTION_ID])
                ->first();

	$Addons_dips = TableRegistry::get('Addons');
         $addonsArr_dips = $Addons_dips->find()
                ->where(['Addons.main_category_id ' =>DIPPINGSAUCEADDONCATEGORY_ID])
                ->order(['Addons.order'=> 'asc'])
                ->all();

    if($delLoc == OTTAWA_ID)
    {
        $addonsArr_dips = $Addons_dips->find()
        ->where(['Addons.main_category_id ' =>DIPPINGSAUCEADDONCATEGORY_ID,'Addons.id NOT IN' =>unserialize(OTTAWA_REMOVE_DIP)])
                ->order(['Addons.order'=> 'asc'])
                ->all();
    }


        foreach ($addonsArr_dips as $key => $value) {
            
            $dipsArr[] = array('id' => $value['id'], 'name' => $value['name'],'price' => $value['price'],'description'=>$value['description']);
	    
        }


	
     
        $this->set('addon_dips', $addon_dips);
        $this->set('addon_toppings', $addon_toppings);
        $this->set('addon_free_toppings', $addon_free_toppings);
        $this->set('addon_special', $addon_special);
       
        $this->set('addon_type_prices', $addon_type_prices);

        $this->set('addon_maincat', $addon_maincat);
        $this->set('addonCategories', $addonCategories);
        $this->set('addons', $query);
        $this->set('product', $product);
	$this->set('dipsArr', $dipsArr);

        $this->set('_serialize', ['product']);
	

         

        $session_key = "";

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
         
           
            $selected_items = $cart_items['demo'][$key][$id][$productType][$pizza_selection];
          
            
            $this->set('selected_itemArr', $cart_items['demo'][$key][$id]);
            $session_key = $key;
            if($productType=="wings")
            {
              $selected_items = $cart_items['demo'][$key][$id][$productType];
            }

            if($productType=="pop")
            {
              $selected_items = $cart_items['demo'][$key][$id][$productType];
            }
	   if($productType=="dip")
            {
              $selected_items = $cart_items['demo'][$key][$id][$productType];
            }
	if($productType=="lasagana")
            {
              $selected_items = $cart_items['demo'][$key][$id][$productType];
            }
	  if($productType=="side")
            {
              $selected_items = $cart_items['demo'][$key][$id][$productType];
            }
            $this->set('selected_items', $selected_items);

			//echo '<pre>';print_r($cart_items['demo'][$key][$id]);exit;
           //  echo '<pre>';print_r($cart_items['demo'][$key][$id]);exit;
            $editComboInitalPrice = $cart_items['demo'][$key][$id][combo_details][final_price];
    
            $this->set('editComboInitalPrice', $editComboInitalPrice);
            $combo_start_price = $cart_items['demo'][$key][$id][combo_details][start_price];

            $combo_size = $cart_items['demo'][$key][$id][combo_details][size];

            if (isset($p2)) {
                                    $index=$p2-1;

                    $combo_current_pizza_price = $cart_items['demo'][$key][$id][$index][total_price];

                // if ($p2 == 1) {
                //     $index=$p2-1;
                //     $combo_current_pizza_price = $cart_items['combo'][$key][$id][1][total_price];
                // }
                // if ($p2 == 2) {
                //     $combo_current_pizza_price = $cart_items['combo'][$key][$id][0][total_price];
                // }
            }
            //$combo_current_price=$editComboInitalPrice;
            $combo_current_price=$combo_start_price;
			
			

       //echo '<pre>';print_r( $cart_items['demo']);exit;
         
            if($productType=="pizza")
            {
                foreach ($cart_items['demo'][$key][$id][pizza] as $ind =>$value) 
                {
                    if($ind!=$index)
                    {
                        $combo_current_price+=$value['total_price'];
                    }
                    
                }

                $combo_current_price+=$cart_items['demo'][$key][$id][wings]['final_price'];
                $combo_current_price+=$cart_items['demo'][$key][$id][pop]['total_price'];
				$combo_current_price+=$cart_items['demo'][$key][$id][dip]['total_price'];
				$combo_current_price+=$cart_items['demo'][$key][$id][side]['total_price']; 
				$combo_current_price+=$cart_items['demo'][$key][$id][lasagana]['total_price']; 

             }

             elseif($productType=="wings")
             {
                foreach ($cart_items['demo'][$key][$id][pizza] as $ind =>$value) 
                {
                    
                        $combo_current_price+=$value['total_price'];
                    
                }
                $combo_current_price+=$cart_items['demo'][$key][$id][pop]['total_price'];
		$combo_current_price+=$cart_items['demo'][$key][$id][dip]['total_price'];
		$combo_current_price+=$cart_items['demo'][$key][$id][side]['total_price'];
		$combo_current_price+=$cart_items['demo'][$key][$id][lasagana]['total_price']; 
             } 
             elseif($productType=="pop")
             {
                foreach ($cart_items['demo'][$key][$id][pizza] as $ind =>$value) 
                {
                    
                        $combo_current_price+=$value['total_price'];
                    
                }
                 $combo_current_price+=$cart_items['demo'][$key][$id][wings]['final_price'];
		 $combo_current_price+=$cart_items['demo'][$key][$id][dip]['total_price'];
		 $combo_current_price+=$cart_items['demo'][$key][$id][side]['total_price'];
		 $combo_current_price+=$cart_items['demo'][$key][$id][lasagana]['total_price']; 
//echo '<pre>';print_r($cart_items['demo'][$key][$id]);
//echo $combo_current_price;
             }
            elseif($productType=="dip")
		{

			foreach ($cart_items['demo'][$key][$id][pizza] as $ind =>$value) 
		        {
		            
		                $combo_current_price+=$value['total_price'];
		            
		        }
		         $combo_current_price+=$cart_items['demo'][$key][$id][wings]['final_price'];
			 $combo_current_price+=$cart_items['demo'][$key][$id][pop]['total_price'];
			 $combo_current_price+=$cart_items['demo'][$key][$id][side]['total_price'];
			 $combo_current_price+=$cart_items['demo'][$key][$id][lasagana]['total_price']; 
		}
             elseif($productType=="lasagana")
		{

			foreach ($cart_items['demo'][$key][$id][pizza] as $ind =>$value) 
		        {
		            
		                $combo_current_price+=$value['total_price'];
		            
		        }
		         $combo_current_price+=$cart_items['demo'][$key][$id][wings]['final_price'];
			 $combo_current_price+=$cart_items['demo'][$key][$id][pop]['total_price'];
			 $combo_current_price+=$cart_items['demo'][$key][$id][side]['total_price'];
			 $combo_current_price+=$cart_items['demo'][$key][$id][dip]['total_price']; 
		}
	   else
            {
			foreach ($cart_items['demo'][$key][$id][pizza] as $ind =>$value) 
		        {
		            
		                $combo_current_price+=$value['total_price'];
		            
		        }
		         $combo_current_price+=$cart_items['demo'][$key][$id][wings]['final_price'];
			 $combo_current_price+=$cart_items['demo'][$key][$id][pop]['total_price'];
			 $combo_current_price+=$cart_items['demo'][$key][$id][dip]['total_price']; 
			 $combo_current_price+=$cart_items['demo'][$key][$id][lasagana]['total_price']; 
            }
        }

      
        if($productType=="pizza")
        {
            $ComboProducts = TableRegistry::get('ComboProducts');
            $combo_products = $ComboProducts->find("all")
                ->where(['ComboProducts.combo_id' => $id,'ComboProducts.product_category_id' => PIZZA_ID,'ComboProducts.size' => $p2])
                ->all();
            foreach ($combo_products as $key => $value) 
            {
             $PizzaDefaultAddonsArr=explode(',',$value['addons']);
            }
            $this->set('default_addons', $PizzaDefaultAddonsArr);
            //echo '<pre>';print_r($PizzaDefaultAddonsArr); exit;


        }


       
        $oaddons = $Addons->find("all")
                ->where(['Addons.addon_category_id' => DRINKS_SIZE]);
        foreach ($oaddons as $key => $value) {
            $sizeaddonsArr[$value['id']] = $value['name'];
        }
        $this->set('sizeaddonsArr', $sizeaddonsArr);


        // $combo_current_price = $combo_current_pizza_price + $combo_start_price;

        $this->set('combo_current_price', $combo_current_price);
        $this->set('combo_size', $combo_size);
        
        $this->set('session_key', $session_key);

        $Addons = TableRegistry::get('Addons');
        $oaddons = $Addons->find("all")
                ->where(['Addons.addon_category_id' => PARTYWINGADDONCATEGORY_ID]);
        foreach ($oaddons as $key => $value) {
            $oaddonsArr[$value['id']] = $value['name'];
        }
        $this->set('oaddonsArr', $oaddonsArr);
         $this->set('comboItem', $comboItem);
        $this->set('p2', $p2);
		
		/** only for side orders **/
        $ComboAddons = TableRegistry::get('ComboAddons');
		$side_addons = $ComboAddons->find()->where(['ComboAddons.combo_id' => $id])->all();
        foreach ($side_addons as $key => $value) {
            $side_addons_list[] = $value['addon_id'];
        }
        
        $side_all = $Addons->find()->where(['Addons.id IN' => $side_addons_list])->order(['Addons.order' => asc])->all();
		$this->set('side_all', $side_all);		
    }
	
	/**
	 * @csp
	 * calculating the combo price
	**/
	
	public function combo_calculate()
    {
 
    	$product_count = $_POST['product_count'];
    	$product_id    = $_POST['product_id'];
		$ini_total     = $total  	   = $_POST['start_price'];
        $size   	   = $_POST['size'];
		$product_combo_id = $_POST['product_combo_id'];
		
		$combocount    = 1;
        $combocheck    = 0;
		$checkflag     = true;
		$firstarray = unserialize(DOUBLE_ID); 
		$Addons 	   = TableRegistry::get('Addons');
		$addons   	   = $Addons->find();
		$addons_count       = array();		 		 
        /*foreach($addons as $key1 => $value1)
			{
			
				$addons_count[$value1->id]= $value1->topping_count;
			}*/
		
		
		$ToppingPrice 	= TableRegistry::get('ToppingPrice');
		$toppingsArr    = $ToppingPrice->find();	

        $toppings       = array();
        $flag           = 0;
        foreach($toppingsArr as $key => $value)
			{
                $flag++;
                if($flag==1)
                {
                    $id=$value->addon_id;
                }
				$toppings[$value->size][$value->type][$value->addon_id] = $value->price;
			}
		$defaultsetPrice = $toppings[$size]['full'][$id];
		
		$Combo 		  = TableRegistry::get('Combo');
		$productcombo = $Combo->find()->where(['Combo.id' => $product_combo_id])->first();
	    $topNo 		  = $productcombo->count;	
			

        $premiumprice = 0;
       
		foreach ($_POST['addon'] as $key => $main)
        {
			foreach ($main as $key1 => $subcat)
			{
				$side         =  $subcat['side'];
				$addon_id     = $subcat['addon_id'];
				$singletopCnt = $subcat['type'][0];

                
				//$premium = $addons_count[$addon_id];
				if(in_array($addon_id,$firstarray))
                {
                   $premium = 2;
                }
                else
                {
                   $premium = 1;
                }
				$singletopCnt = $singletopCnt*$premium;
						
				
				
				if($side=="left" || $side=="right")
				{
					$side1="half";
					$chkside = 'half';
					if(isset($subcat['type']) && $subcat['type']!=''):
					    
						$combocheck += 0.5*$singletopCnt;
					endif;
					
					$addon_price =   $toppings[$size][$chkside][$addon_id];
				}
				else
		        	{
				   $side1="full";
				   $chkside = 'full';
				   if(isset($subcat['type']) && $subcat['type']!=''):
				    
					$combocheck += 1*$singletopCnt;
				   endif;
				   
				   $addon_price =   $toppings[$size][$chkside][$addon_id];
				}
				$type        =  $subcat['type'];
				
				$price     = 0.00;

				//echo $size.'-';
			//	echo '<pre>';print_r($toppings[$size][$chkside][$addon_id]);

				if($combocheck>$topNo && $checkflag==true)
				{
					$singletopCnt = $combocheck-$topNo;
					if($chkside!='full')
					{
					  $singletopCnt = $singletopCnt/0.5;
					}
					
					$checkflag      = false;
					$price          =  $addon_price*$singletopCnt;
				}
				elseif($combocheck>$topNo && $checkflag==false)
				{
					$price          =  $addon_price*$singletopCnt;
				}
				
				//echo 	 $price.'-'.$addon_price.'|';
				
				/*$addon_price =   $toppings[$size][$chkside]*$subcat['type'][0];
				if(in_array($addon_id,$firstarray))
					{
						$addon_price = $addon_price*2;
					}
				elseif($addon_id==$trippleID)
				{
					$addon_price = $addon_price*3;
				}*/
					
					
				if(isset($price) && $price!='' && $type!='')
				{
					$total+=$price;
				}
			}
		}
		
		
		/*if($combocheck>$topNo)
		{
			//echo 	 $combocheck.'-'.$total.'|';
			$setPrice        = $defaultsetPrice*$topNo;
		
			$checktotal = $total-$setPrice ;
			$total 		= $checktotal ; 
		}
		else
		{
			$total = $initial_total ; 
		}*/

        $session      = $this->request->session();
        $n_cart_items = $session->read('Cartarray1');

       
         if(isset($_POST['dips']))
         {
            foreach ($_POST['dips'] as $key => $value)

            {
                $addon_id  =  $value['addon_id'];
				$addon_dip_price_details['price'] = '';
				if($key != DOUGH_ID)
				   {
							 
						if(isset($addon_id) && $addon_id!= PIZZA_SAUCE_ID && $addon_id!= NO_SAUCE_ID)
						{
							
							$addon_dip_price_details['price'] = $defaultsetPrice; 
						}
						
				    }
                else
					{
						if($addon_id == GLUTEN_ID)
							{
								$addon_dip_price_details['price'] = GLUTEN_PRICE ; 
							}
					}
                if(isset($addon_dip_price_details['price']))
                 {
                    $addon_dip_price = $addon_dip_price_details['price'];
                    $total+=$addon_dip_price;
                 }
            }
        }

        if(isset($_POST['special']['addon_id']))
         {
            
                $addon_id=$_POST['special']['addon_id'];
                $addon_spl_price_details = $Prices->find()
                ->where(['Prices.product_id' =>$product_id,'Prices.addon_id' =>$addon_id,'Prices.size' =>$size])
                 ->first();
                if(isset($addon_spl_price_details['price']))
                 {
                    $addon_spl_price=$addon_spl_price_details['price'];
                    //echo $addon_price; exit();
                     $total+=$addon_spl_price;
                 }
            
        }

        if(isset($_POST['crust']))
        {
            $price=CRUST_PRICE;
            $crust=$_POST['crust'];
            if($crust=="thick")
            {
                $total+=$price;
            }

        }

        $final=$total*$product_count;

        $final= number_format((float)$final, 2, '.', '');
		echo $final;exit;
        
    }
	
	
	public function combo_drink_price() {
        

        $drinks_size = $_POST['drinks_size'];
        $drinks_type = $_POST['drinks_type'];
        $pop_size    = $_POST['pop_size'];
        $delLoc  = $this->request->session()->read('Config.location');
        $product_count = $_POST['product_count'];
        $total_drinks  = count($_POST['drinks_type']);
		$product_id    = $_POST['product_combo_id'];


        $sizeCount      = 0;
        $sizeCounttotal = 0;
        $totalFlag      = True;

        $Combos 	 = TableRegistry::get('Combos');
		
        foreach($drinks_type as $key=>$value)
        {
            $drinks_count = $_POST['drinks_count'][$value];

            if($drinks_count!='' && $drinks_count!=0):
	             $sizeCount  = $drinks_count;
			endif;
        
			$sizeCounttotal += $sizeCount;

           if($sizeCounttotal>=$pop_size && $totalFlag == true)
             {
                 $sizeCount = $sizeCounttotal-$pop_size;
                 $totalFlag = false;
             }

           $addon_price = $Combos->find()->where(['Combos.type' =>'drink','Combos.size' => $drinks_size,'Combos.name' => $value,'Combos.restaurants_id' => $delLoc])->last();
            
	   if (isset($addon_price['price']) && $sizeCounttotal > $pop_size) 
             {
               
                $price         = $addon_price['price'];
                $final_price   = $sizeCount * $price;
                $total+= $final_price;

               
             }
        }
          
      
        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        echo $final;exit;
       
    }
	
	 public function refreshcombocart()
    {
        exit;
        $size			 =	$_POST['size'];
        $pizza_selection =	$_POST['pizza_selection'];
        $combo_start_price = $_POST['combo_start_price'];
        $session_key	 =	$_POST['session_key'];
        $product_combo_id  = $_POST['product_combo_id'];
        $session 		 = $this->request->session();
        $cart_items		 = $session->read('Cartarray1');
		
		
		
		$ToppingPrice 	= TableRegistry::get('ToppingPrice');
		$toppingsArr    = $ToppingPrice->find();	

        $toppings       = array();
        foreach($toppingsArr as $key => $value)
			{
				$toppings[$value->size][$value->type][$value->addon_id] = $value->price;
			}
		$defaultsetPrice = $toppings[$size]['full'];
		
		
		$Addons 		= TableRegistry::get('Addons');
		$addons    		= $Addons->find();
		$addons_count   = array();		 		 
        /*foreach($addons as $key1 => $value1)
			{
			
				$addons_count[$value1->id]= $value1->topping_count;
			}*/
        
		$Combo 		  = TableRegistry::get('Combo');
		$productcombo = $Combo->find()->where(['Combo.id' => $product_combo_id])->first();
	    $topNo 		  = $productcombo->count;	
		
		$firstarray = unserialize(DOUBLE_ID);
		//$trippleID  = TRIPPLE_ID;
  
        $dec	=	1;
        $initial_total = $total  =	0;
        $total_pizza_price = 0;
        if($pizza_selection==1)
        {
            $index=$pizza_selection-$dec;
            $index2 = $pizza_selection+1;
        }
        elseif($pizza_selection==0)
        {
            $index=$pizza_selection+$dec;
            $index2 = $pizza_selection+2;
        }
        else
        {
             $index=$pizza_selection-2;
             $index2 = $pizza_selection-1;
        }         

        $combocount  = 1;
        $cheesecount = 0;
        $combocheck  = 0;
        $ik = 1;
		$checkflag     = true;

		// echo '<pre>';print_r($cart_items['demo'][$session_key][$product_combo_id]['pizza']) ;exit;
		
        if(!isset($cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index]['toppings']) && !isset($cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index2]['toppings']))
        {
            echo "no change";exit;
        }

        $var=$cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index]['toppings'];
		$dipvar = $cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index]['dips'];
		
		$pcrust = $cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index]['crust'];
		$crustPrice=0.00;
		if($pcrust=='thick')
		{
			$crustPrice = CRUST_PRICE;
		}
        $i=0;
       

        foreach ($var as $key => $value) 
        {

            $addon_cat=$value['addon_cat'];
            $k=0;
            foreach ($value['addon_subcat'] as $skey => $svalue) 
            {
                
                $type=$svalue[addonnames]['type'];
                $addon_id=$svalue[addonnames]['id'];
                $side=$svalue[addonnames]['side'];
				$singletopCnt = $type[0];
				
				
				
				//$premium = $addons_count[$addon_id];
				//$premium = 1;
                //$singletopCnt = $singletopCnt*$premium;
                

                if(in_array($addon_id,$firstarray))
				{
					$premium = 2;
					$singletopCnt = $singletopCnt*$premium;
				}
				elseif($addon_id==$trippleID)
				{
					$premium = 3;
					$singletopCnt = $singletopCnt*$premium;
				}
				else
				{
					$premium = 1;
					$singletopCnt = $singletopCnt*$premium;
				}
              
                if ($side == "left" || $side == "right")
                {
					$side1 = "half";
					$chkside = 'half';
					$combocheck += 0.5*$singletopCnt;
                } 
				else 
                  {
                        $side1 = "full";
                        $chkside = 'full';
						//  $singletopCnt = $type[0];
						$combocheck += 1*$singletopCnt;
                   }
                      
				$addon_price =   $toppings[$size][$chkside][$addon_id];
				if($addon_id ==CRUST_CHILLY)
				{
					$addon_price  = 0;
				}
				
				$price     = 0.00;
				if($combocheck>$topNo && $checkflag==true)
				{
					$singletopCnt = $combocheck-$topNo;
					if($chkside!='full')
					{
					  $singletopCnt = $singletopCnt/0.5;
					}
					
					$checkflag      = false;
					$price          =  $addon_price*$singletopCnt;
				}
				elseif($combocheck>$topNo && $checkflag==false)
				{
					$price          =  $addon_price*$singletopCnt;
				}
				
				if(isset($price) && $price!='' && $type!='')
				{
					$total+=$price;
					$cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index]['toppings'][$i][addon_subcat][$k][addonnames][price]=$addon_price;
					$combocount++;
				}
				
					
            $k++;
            }
        $i++;
        }
		 
		$ii=0; 
               $Dpricetotal = 0.00;
		foreach ($dipvar as $key => $value) 
        {

            $addon_cat=$value['addon_cat'];
            $k=0;
            foreach ($value['addon_subcat'] as $skey => $svalue) 
            {

                $addon_id=$svalue[addonnames]['addon_id'];
				$Dprice  = 0.00; 
				if($addon_cat != DOUGH_ID)
					{
						if($addon_id != PIZZA_SAUCE_ID && $addon_id!= NO_SAUCE_ID)
						{
							//$defaultsetPrice = $toppings[$size]['full'];
							$defaultsetPrice = $toppings[$size]['full'][795];
							$Dprice 		 = $defaultsetPrice;
						}
					}
				else
				  {
					if($addon_id == GLUTEN_ID)
					{
						$Dprice  = GLUTEN_PRICE ; 
					}
					else
					{
						$Dprice  = 0.00; 
					}
				  }
             $Dpricetotal+= $Dprice;
            $k++;
            }
        $ii++;
        }
		//echo $combocheck.'>'.$topNo;
		/*if($combocheck>$topNo)
		{
			
			$setPrice        = $defaultsetPrice*$topNo;
			$checktotal = $total-$setPrice ;
			$total 		= $checktotal ; 
		}
		else
		{
			$total = $initial_total ; 
		}*/
		
		$total += $Dpricetotal;
		$total += $crustPrice;

		
		
        $var1=$cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index2]['toppings'];
		$dipvar1 = $cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index2]['dips'];
		$pcrust = $cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index2]['crust'];
		$crustPrice=0.00;
		if($pcrust=='thick')
		{
			$crustPrice = CRUST_PRICE;
		}

        $initial_total1 =  $total1=0;
        $i=0;
        $combocheck = 0;
		$checkflag     = true;
		
		
        foreach ($var1 as $key => $value) 
        {

            $addon_cat=$value['addon_cat'];
            $k=0;
            foreach ($value['addon_subcat'] as $skey => $svalue) 
            {
                $side=$svalue[addonnames]['side'];
                $type=$svalue[addonnames]['type'];
                $addon_id=$svalue[addonnames]['id'];
                $side=$svalue[addonnames]['side'];
				
				$singletopCnt = $type[0];
				
				//$premium = $addons_count[$addon_id];
				 if(in_array($addon_id,$firstarray))
                {
                    $premium = 2;
                    $singletopCnt = $singletopCnt*$premium;
                }
                elseif($addon_id==$trippleID)
                {
                    $premium = 3;
                    $singletopCnt = $singletopCnt*$premium;
                }
                else
                {
                    $premium = 1;
                    $singletopCnt = $singletopCnt*$premium;
                }
				$singletopCnt = $singletopCnt*$premium;
				
                if ($side == "left" || $side == "right")
                  {
                        $side1 = "half";
                        $chkside = 'half';
                       // $singletopCnt = $type[0];
						$combocheck += 0.5*$singletopCnt;
                  } 
				else 
                  {
                        $side1 = "full";
                        $chkside = 'full';
                       // $singletopCnt = $type[0];
						$combocheck += 1*$singletopCnt;
                   }

				$addon_price =   $toppings[$size][$chkside][$addon_id];

				if($addon_id ==CRUST_CHILLY)
				{
					$addon_price  = 0;
				}
				
				$price     = 0.00;
				if($combocheck>$topNo && $checkflag==true)
				{
					$singletopCnt = $combocheck-$topNo;
					if($chkside!='full')
					{
					  $singletopCnt = $singletopCnt/0.5;
					}
					
					$checkflag      = false;
					$price          =  $addon_price*$singletopCnt;
				}
				elseif($combocheck>$topNo && $checkflag==false)
				{
					$price          =  $addon_price*$singletopCnt;
				}
				
				if(isset($price) && $price!='' && $type!='')
				{
					$total1+=$price;
					$cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index2]['toppings'][$i][addon_subcat][$k][addonnames][price]=$addon_price;
                    $combocount++;
				}
				
				
				/*if(isset($addon_price) && $addon_price!='' && $type!='')
				{
					   $total1+=$addon_price*$type[0];
					   $cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index2]['toppings'][$i][addon_subcat][$k][addonnames][price]=$addon_price;
                       $combocount++;
				}*/

                    
            $k++;
            }
        $i++;
        }
		
		
		$i=0; 
		$Dprice  = 0.00; 
		$Dpricetotal =0.00; 
		foreach ($dipvar1 as $key => $value) 
        {
	   
            $addon_cat=$value['addon_cat'];
            $k=0;
          
            foreach ($value['addon_subcat'] as $skey => $svalue) 
            {

                $addon_id=$svalue[addonnames]['addon_id']; 
				
				if($addon_cat != DOUGH_ID)
					{
						if($addon_id != PIZZA_SAUCE_ID && $addon_id!= NO_SAUCE_ID)
						{
							$defaultsetPrice = $toppings[$size]['full'][795];
							$Dprice 		 = $defaultsetPrice;
                                                        
						}
					}
				else
				  {
					if($addon_id == GLUTEN_ID)
					{
						$Dprice  = GLUTEN_PRICE ; 
					}
					else
					{
						$Dprice  = 0.00; 
					}
				  }
		$Dpricetotal+= $Dprice;
            $k++;
            }
        $i++;
        }
		
		/*if($combocheck>$topNo)
		{
			
			$setPrice   = $defaultsetPrice*$topNo;
			$checktotal = $total1-$setPrice ;
			$total1 		= $checktotal ; 
		}
		else
		{
			$total1 = $initial_total1 ; 
		}*/
		$total1 += $Dpricetotal;
		$total1 += $crustPrice;
		
		//echo $total.'-'.$total1.'|'.$Dprice;
 
  
        $cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index]['total_price']=$total;


         $cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index2]['total_price']=$total1;

        $start_price=$cart_items['demo'][$session_key][$product_combo_id]['combo_details']['start_price'];




        foreach ($cart_items['demo'][$session_key][$product_combo_id]['pizza'] as $key => $value) 
        {
            $total_pizza_price+=$value[total_price];
        }
     
        $final=$total_pizza_price+$start_price;
        $cart_items['demo'][$session_key][$product_combo_id]['combo_details']['final_price']=$final;
        $cart_items['demo'][$session_key][$product_combo_id]['combo_details']['size']=$size;
        $cart_items['demo'][$session_key][$product_combo_id]['pizza'][0][size]=$size;
        $cart_items['demo'][$session_key][$product_combo_id]['pizza'][1][size]=$size;


        $sessionData = $session->write('Cartarray1',$cart_items);
        $session = $this->request->session(); 
        $cart_items=$session->read('Cartarray1');
//echo '<pre>';print_r($cart_items);
       
        exit;
    }
	
	
	public function refreshcomboprice()
    {
        $pizza_selection	=	$_POST['pizza_selection'];
        $combo_start_price	=	$_POST['combo_start_price'];
        $session_key		=	$_POST['session_key'];
        $product_combo_id	=	$_POST['product_combo_id'];
        $session 			=	$this->request->session();
        $cart_items			=	$session->read('Cartarray1');

        //echo '<pre>';print_r($cart_items) ;exit;
        $dec=1;
        if($pizza_selection==1)
        {
            $index=$pizza_selection-$dec;
            $index2 = $pizza_selection+1;

        }
        elseif($pizza_selection==0)
        {
            $index=$pizza_selection+$dec;
            $index2 = $pizza_selection+2;
        }
        else
        {
             $index=$pizza_selection-2;
             $index2 = $pizza_selection-1;
        }    
     

        $wingfinal = 0; 
        $popfinal = 0; 
        $dipfinal = 0; 
	$sidefinal = 0; 



        if(isset($cart_items['demo'][$session_key][$product_combo_id]['wings']['final_price']) && $cart_items['demo'][$session_key][$product_combo_id]['wings']['final_price']!=''):
          $wingfinal = $cart_items['demo'][$session_key][$product_combo_id]['wings']['final_price'];
        endif;

        if(isset($cart_items['demo'][$session_key][$product_combo_id]['pop']['total_price']) && $cart_items['demo'][$session_key][$product_combo_id]['pop']['total_price']!=''):
          $popfinal = $cart_items['demo'][$session_key][$product_combo_id]['pop']['total_price'];
        endif;

          if(isset($cart_items['demo'][$session_key][$product_combo_id]['dip']['total_price']) && $cart_items['demo'][$session_key][$product_combo_id]['dip']['total_price']!=''):
          $dipfinal = $cart_items['demo'][$session_key][$product_combo_id]['dip']['total_price'];
        endif;

	 if(isset($cart_items['demo'][$session_key][$product_combo_id]['side']['total_price']) && $cart_items['demo'][$session_key][$product_combo_id]['side']['total_price']!=''):
          $sidefinal = $cart_items['demo'][$session_key][$product_combo_id]['side']['total_price'];
        endif;

  

        $finalstart_price=$combo_start_price+$cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index]['total_price']+$cart_items['demo'][$session_key][$product_combo_id]['pizza'][$index2]['total_price']+$wingfinal+$popfinal+$dipfinal+$sidefinal;


        echo $finalstart_price;exit;

    }


public function combo_dip_price() {
        
        $drinks_type = $_POST['dips_type'];
        $delLoc  = $this->request->session()->read('Config.location');
        $product_count = $_POST['product_count'];
        $total_drinks  = count($_POST['dips_type']);
	    $product_id    = $_POST['product_combo_id'];

	    $Combos = TableRegistry::get('Combo');
        $product = $Combos->find("all")->where(['Combo.id' => $product_id])->first();

	    $default_dips = $product->dippingsauce_count;


        $sizeCount      = 0;
        $sizeCounttotal = 0;
        $totalFlag      = True;

		
        foreach($drinks_type as $key=>$value)
        {
            $drinks_count = $_POST['drinks_count'][$value];

            if($drinks_count!='' && $drinks_count!=0)
		{
		   $sizeCount  = $drinks_count;
		}
	    $sizeCounttotal += $sizeCount;

           if($sizeCounttotal>=$default_dips && $totalFlag == true)
             {
                 $sizeCount = $sizeCounttotal-$default_dips;
                 $totalFlag = false;
             }


              if($delLoc == OTTAWA_ID)
                {
                    $addon_price = $_POST['drinks_price'][$value];
                    $addon_price = $addon_price+OTTAWA_DIP_DIFFRENCE;
                }
               else if($delLoc == MARKHAM_ID)
                {
                    $addon_price = $_POST['drinks_price'][$value];
                }
                else if($delLoc == DANFORTH_ID)
                {
                    $addon_price = $_POST['drinks_price'][$value];
                    $addon_price = $addon_price+DANFORTH_DIP_DIFFRENCE;
                }
                else
                {
                    $addon_price = $_POST['drinks_price'][$value]; 
                }



           
            
	   if (isset($addon_price) && $sizeCounttotal > $default_dips) 
             {
               
                $price         = $addon_price;
                $final_price   = $sizeCount * $price;
                $total+= $final_price;

               
             }
        }
          
      
        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        echo $final;exit;
       
    }
	
	/**
	 *@csp Jul-09
	 *for calculate the toppings price
	 *return configuration details
	**/
    public function topping_price(){
        $this->layout = 'admin';

        $ToppingPrice = TableRegistry::get('ToppingPrice');
        $Addons       = TableRegistry::get('Addons');
        $PizzaSizes   = TableRegistry::get('pizza_sizes');
        $AddonCats    = TableRegistry::get('addon_categories');

        //--- TOPPING CATEGORIES ---\\
        
        $toppingCats = $AddonCats->find()
                        ->select(['id', 'name'])
                        ->where(['parent_id' => TOPPINGS_ID])
                        ->hydrate(false)
                        ->toArray();

        // --- ALL TOPPINGS ---\\

        $allSizes = $PizzaSizes->find()->hydrate(false);
        $allToppings = $Addons->find()
                ->select(['id', 'name', 'addon_category_id', 'full_image', 'half_image'])
                ->where(['Addons.main_category_id' => TOPPINGS_ID])
                ->hydrate(false)
                ->toArray();
        $toppingPricesArray = $ToppingPrice->find()
                ->hydrate(false)
                ->toArray();
        foreach($toppingPricesArray as $tp){
            $toppingPrices[$tp['addon_id']][$tp['size']][$tp['type']] = $tp['price'];
        }

        //Creates one entry for each topping category
        foreach($toppingCats as $tc){
            $displayedResults[$tc['id']]["name"] = $tc['name'];
            $displayedResults[$tc['id']]["toppings"] = array();
        }

        foreach($allToppings as $key => $topping){
            foreach($allSizes->toArray() as $size){ 
                $allToppings[$key]['prices'][$size['size_value']] = array(
                    "label"=>$size['size_label'], 
                    "half_price"=>$toppingPrices[$topping['id']][$size['size_value']]["half"], 
                    "full_price"=>$toppingPrices[$topping['id']][$size['size_value']]["full"]
                );
            }
            array_push($displayedResults[$topping['addon_category_id']]["toppings"], $allToppings[$key]);
        }
        // echo "<PRE>";
        // var_dump($displayedResults);
        // exit;
                        
        //--- ---\\

        $this->set('displayedResults', $displayedResults);
        $this->set('toppingCategories', $toppingCats);
        $this->set('numToppingCats', sizeof($toppingCats));
    }

    public function toppings_add(){
        $this->layout = 'admin';
        
        $Addons = TableRegistry::get('Addons');
        $addon = $Addons->newEntity();
        if ($this->request->is('post')) {

            if(isset($_FILES)) {
                $directory = WWW_ROOT.'addons';
                if(isset($_FILES['half_image']))
                {
                    echo "HAF IMAG!";
                    $fileObject = $_FILES['half_image'];
                    
                    $filetype = $fileObject['type'];
                    $filesize = $fileObject['size'];
                    $filename = $fileObject['name'];
                    $filetmpname = $fileObject['tmp_name'];
                    move_uploaded_file($filetmpname, $directory.'/'.$filename);  
                    $this->request->data['half_image']=$filename;
                } else {
                    $this->request->data['half_image']=addon_noimage;
                }
                
                if(isset($_FILES['full_image']))
                {
                    echo "FOL IMAG!";
                    $fileObject = $_FILES['full_image'];

                    $filetype = $fileObject['type'];
                    $filesize = $fileObject['size'];
                    $filename = $fileObject['name'];
                    $filetmpname = $fileObject['tmp_name'];
                    move_uploaded_file($filetmpname, $directory.'/'.$filename);  
                    $this->request->data['full_image']=$filename;

                } else {
                    $this->request->data['full_image']=addon_noimage;
                }
            } 
            
            // if(isset($_FILES['half_image']))
            // {
            //     $directory = WWW_ROOT.'addons';
            //     $fileObject = $_FILES['half_image'];
            //     $filetype = $fileObject['type'];
            //     $filesize = $fileObject['size'];
            //     $filename = $fileObject['name'];
            //     $filetmpname = $fileObject['tmp_name'];
            //     move_uploaded_file($filetmpname, $directory.'/'.$filename);  
            //     $this->request->data['half_image']=$filename;
            // }

            // if(isset($_FILES['full_image']))
            // {
            //     $directory = WWW_ROOT.'addons';
            //     $fileObject = $_FILES['full_image'];
            //     $filetype = $fileObject['type'];
            //     $filesize = $fileObject['size'];
            //     $filename = $fileObject['name'];
            //     $filetmpname = $fileObject['tmp_name'];
            //     move_uploaded_file($filetmpname, $directory.'/'.$filename);  
            //     $this->request->data['full_image']=$filename;
            // }
            
            if((!isset($this->request->data['addon_category_id'])) ) {
                $this->request->data['addon_category_id']=$this->request->data['main_category_id'];
            } 
            
            if($this->request->data['addon_category_id']==0) {
                $this->request->data['addon_category_id']=$this->request->data['main_category_id'];
            }

            $addon = $Addons->patchEntity($addon, $this->request->data);
            if ($Addons->save($addon)) {
                $this->Flash->success(__('The addon has been saved.'));
            $result= array('id' =>$addon->id,'name' =>$addon->name,'price'=>$addon->price,'addon_category_id'=>$addon->addon_category_id);
                echo json_encode($result);exit;
            } else {
                echo "error";exit;
                $this->Flash->error(__('The addon could not be saved. Please, try again.'));
            }
        }

        

        // $products = $this->Addons->Products->find('list', ['limit' => 200]);
        // $this->set(compact('addon', 'products'));
        // $this->set('_serialize', ['addon']);

        // $AddonCategories= TableRegistry::get('AddonCategories');

        // $addons_categories = $AddonCategories->find("all")
        //     ->where(['type' =>"other",'parent_id' =>0]);

        // $addons_categoriesArr=array();
        // $this->set('addonCategories', $addons_categories);

        // $Categories= TableRegistry::get('Categories');
        // $categories = $Categories->find()
        //     ->where(['Categories.parent_id' =>0 ])
        //     ->all();
        // $this->set('categories', $categories);
    }


    public function toppings_edit($id = null){ 
        $this->layout = 'admin';
        $Addons = TableRegistry::get('Addons');
		$addon = $Addons->get($id, [
            'contain' => []
        ]);
        
        $image="";
        if(isset($addon['image']))
        {
          $image=$addon['image'];
        }
       // echo $image; exit;
        if ($this->request->is(['patch', 'post', 'put'])) {

            if(!empty($_FILES) && ($_FILES['image']['name']!=""))
                {
                  $directory = WWW_ROOT.'addons';
                  $fileObject = $_FILES['image'];
                  $filetype = $fileObject['type'];
                  $filesize = $fileObject['size'];
                  $filename = $fileObject['name'];
                  $filetmpname = $fileObject['tmp_name'];
                  move_uploaded_file($filetmpname, $directory.'/'.$filename);  
                  $this->request->data['image']=$filename;
 
                } 
                else
                {
                  $this->request->data['image']=$image;
                }

                if(!isset($this->request->data['addon_category_id']))
                {
                  $this->request->data['addon_category_id']=$this->request->data['main_category_id'];
                }

                $addon = $Addons->patchEntity($addon, $this->request->data);
                if ($Addons->save($addon)) {
                    $this->Flash->success(__('The addon has been saved.'));
                    $result= array('id' =>$addon->id,'name' =>$addon->name,'price'=>$addon->price);
                    echo json_encode($result);exit; 
                    //return $this->redirect(['controller'=>'Addons','action' => 'index']);
                } else {
                    $this->Flash->error(__('The addon could not be saved. Please, try again.'));
                    echo "error";exit;
                }
        }
    }

    public function topping_price_save(){
        $this->layout = 'admin';

        $dataToSave = array();

        $form_data = $type=$this->request->data;
        $priceType = ["half","full"];
        foreach($priceType as $type){
            foreach($form_data[$type] as $addonKey => $sizes){
                foreach($sizes as $size => $price){
                    if($price != ''){
                        array_push($dataToSave,[
                            "type"      => $type,
                            "size"      => $size,
                            "price"     => $price,
                            "addon_id"  => $addonKey
                        ]);
                    }
                }
            }
        }

        $Topping_price_table = TableRegistry::get('topping_price');
        $toppingPricesList = $Topping_price_table->newEntities($dataToSave);
        
        $Topping_price_table->connection()->transactional(function () use ($Topping_price_table, $toppingPricesList) {
            $Topping_price_table->deleteAll();
            foreach ($toppingPricesList as $tp) {
                $Topping_price_table->save($tp, ['atomic' => false]);
            }
        });
        
        echo "Success!";
        exit;
    }


/**
 *@csp Jul-25
 *for calculate the side dish price
 *return configuration details
**/
public function combo_side_price() {
        
        $drinks_type   = $_POST['dips_type'];

        $product_count = $_POST['product_count'];
        $total_drinks  = count($_POST['dips_type']);
		$product_id    = $_POST['product_combo_id'];
		$side_size     = $_POST['psize'];
        $product_name  = $_POST['pside'];
		$total         = 0;

		$salad_addons  = array();
		$salad_addons  = explode(',',$_POST['addon_id']);

		$final         = 0;
        $totalFlag     = True;
        
	if($side_size=='large')
	{
		//if($product_id==TORONTO_SAUCY_ID || $product_id==OTTAWA_SAUCY_ID)
		//{
              if($product_name!='Salad')
                {
					foreach ($_POST['addons'] as $key => $main)
						{
							foreach ($main as $key1 => $subcat)
							{    
								if (isset($subcat['addon_id'])) 
								 {
									 $total+=   $subcat['lprice'];
								 }
							}
						}
                }
              else
               {

				   foreach ($salad_addons as $key1 => $value1) 
				   {
						if (isset($value1)) 
						{
						   
								$total+= $_POST['salad_addon'][$value1][$side_size];
						  
						}
					}
				}
		//}
		
		$final += COMBO_FAMILY;
		$final +=$total ;
	}
	else
	{
		//if($product_id==TORONTO_SAUCY_ID || $product_id==OTTAWA_SAUCY_ID)
		//{
			if($product_name!='Salad')
                {
					foreach ($_POST['addons'] as $key => $main)
						{
							foreach ($main as $key1 => $subcat)
							{    
								if (isset($subcat['addon_id'])) 
								 {
									 $total+=   $subcat['price'];
								 }
							}
						}
                }
            else
               {

				   foreach ($salad_addons as $key1 => $value1) 
				   {
						if (isset($value1)) 
						{
						   
								$total+= $_POST['salad_addon'][$value1][$side_size];
						  
						}
					}
				}
		//}		
		$final +=$total ;		
	}



        $final = number_format((float) $final, 2, '.', '');
        echo $final;exit;
       
    }
	
	
	/**
	 *@csp Jul-26
	 *for calculate addon price for make your own pizza
	 *return configuration details
	**/
	public function pizza_calculate_yours() {
		
		$delLoc  = 0;
		$delType = 'pickup';
		
		$delType = $this->request->session()->read('Config.deltype');
		$delLoc  = $this->request->session()->read('Config.location');
		
		
        $product_count  = $_POST['product_count'];
        $product_id 	= $_POST['product_id'];
        $isCheck        = true;
		$initial_total  = $total = $_POST['start_price'];
        $size 			= $_POST['size'];

		
			//////////// vaisakh add top count////////		
		$Addons 		= TableRegistry::get('Addons');
		$addons    = $Addons->find();
		$addons_count       = array();		 		 
        /*foreach($addons as $key1 => $value1)
			{
			
				$addons_count[$value1->id]= $value1->topping_count;
			}*/
		//////////// vaisakh add top count////////

        $PriceRule      = TableRegistry::get('PricingRule');
        $pricsArr       = $PriceRule->find()->where(['restaurants_id'=>$delLoc,'type'=>$delType, 'size'=>$size]);
        
        $priceList      = array();
        foreach($pricsArr as $key => $value)
            {
                $priceList[$value->topping_count] = $value->price;
            }    
		
		$ToppingPrice 	= TableRegistry::get('ToppingPrice');
		$toppingsArr    = $ToppingPrice->find();	

		$flag			= 0;
        $toppings       = array();
        foreach($toppingsArr as $key => $value)
			{
				$flag++;
				if($flag==1)
				{
					$id=$value->addon_id;
				}
				$toppings[$value->size][$value->type][$value->addon_id] = $value->price;
			}
 		             
		$maxCount        = MAX_COUNT;
		$uptotops        = 0;
		$increment       = 0;
		$maketops        = 0;
		$firstarray = unserialize(DOUBLE_ID); 
		$defaulttopps   = $this->Products->find()->where(['id' => $product_id ])->first();	
		$defaultTopscnt = explode(',',$defaulttopps->default_toppings);
		$defdips        = explode(',',$defaulttopps->default_dips);
		
		$defaultsetPrice = $toppings[$size]['full'][$id];
        //$defaultsetPrice = $toppings[$size]['full'];



        foreach ($_POST['addon'] as $key => $main) 
        {
            foreach ($main as $key1 => $subcat) 
            {

                if ($_POST['product_id'])
                {
                    $side = $subcat['side'];
                    $addon_id  = $subcat['addon_id'];
                    $type     = $subcat['type'];
                    if($type!='' && $addon_id!=MAKE_CHEESE_ID && $addon_id!=LIGHT_CHEESE)
                    { 
                        
                        $topsingle = $type[0];
                                
                        if(in_array($addon_id,$firstarray))
                        {
                            $premium = 2;
                            $topsingle = $topsingle*$premium;

                        }
                        elseif($addon_id==$trippleID)
                        {
                            $premium = 3;
                            $topsingle = $topsingle*$premium;
                        }
                        else
                        {
                            $premium = 1;
                            $topsingle = $topsingle*$premium;
                        }   
                                
                        
                        if($side=='full')
                        {
                            $maketops += $topsingle;
                        }
                        else
                        {
                           $maketops += $topsingle;
                        }
                        
                       
                    
                    }
                    
                }
            }
        }

        // if($maketops==1)
        // {
        //     $total = $priceList[1];
        //     $maxCount        = 1;
        // }
        // elseif($maketops ==2)
        // {
        //     $total = $priceList[2];
        //     $maxCount        =3;
        // }
        // elseif($maketops >=3)
        // {
        //     $total = $priceList[3];
        //     $maxCount        = 3;
        // }
        // else
        // {
        //     $maxCount        = 0;
        // }

       

	

        foreach ($_POST['addon'] as $key => $main) 
		{
            foreach ($main as $key1 => $subcat) 
			{
				$addon_price_details['price']   = 0.00;
				$addon_id       				= $subcat['addon_id'];				
				
                if ($_POST['product_id'])
				{
                    $side = $subcat['side'];
                    if ($side == "left" || $side == "right") 
					{
                        $side1 = "full";
                    } else 
					{
                        $side1 = "full";
                    }

                    $type 	  = $subcat['type'];
					
					if($type!='' && $subcat['default']==0)
					{ 
						
						$topsingleCount = $type[0];
						//$premium = $addons_count[$addon_id];
						if(in_array($addon_id,$firstarray))
                            {
                                $premium = 2;
                            }
                            else
                            {
                                $premium = 1;
                            }
						$topsingleCount = $topsingleCount*$premium;
								
						if($side=='full')
						{
							$uptotops += $topsingleCount;
						}
						else
						{
							$uptotops += 1 * $topsingleCount; 
						}
						if($side=='full')
                        {
                            $maketops += $topsingleCount;
                        }
                        else
                        {
                            $maketops += 1 * $topsingleCount; 
                        }
						
							 
						if($uptotops>$maxCount && $isCheck==true)
							{
								$topsingleCount = $uptotops-$maxCount;
								if($side1=='half')
								{
									$topsingleCount = $topsingleCount/1;
								}
								$isCheck      = false;
								$addon_price_details['price'] =  $toppings[$size][$side1][$addon_id];
							}
						elseif($uptotops>$maxCount && $isCheck==false)
							{
								$addon_price_details['price']          =  $toppings[$size][$side1][$addon_id];
							}

							 
						if (isset($addon_price_details['price'])) 
						{	
							
                               $addon_price = $addon_price_details['price']*$topsingleCount; 
                               $total+=$addon_price;
							 
							//echo '('.$addon_id.','.$addon_price.','.$total.')|';
							//$total+=$addon_price;
							//$isCheck  = true;
														
						}
					}
                    
                }
            }
        }

        if (isset($_POST['dips'])) {
            foreach ($_POST['dips'] as $key => $value) {
                
                 /** dips[][addon_id] changd to dips[][addon_id][]  in speciality pizza **/

                  if(isset($_POST['product_combo_id']) && $_POST['product_combo_id'] !=''):
                       $addon_id = $value['addon_id'];
                       $_POST['tagcatid']=SAUCEID;
                  else:
                       $addon_id = $value['addon_id'][0]; 
                  endif;
                 
                $addon_dip_price_details['price'] = '';

                if($key != DOUGH_ID)
				{
                
                    if(isset($value['addon_id'][0]) && $value['addon_id'][0] != PIZZA_SAUCE_ID && $value['addon_id'][0] != NO_SAUCE_ID && !in_array($value['addon_id'][0],$defdips))
                         {
                            $addon_dip_price_details['price'] = $defaultsetPrice ;
                         }
				}
                else
                  {
			         if($value['addon_id'][0] == GLUTEN_ID)
			         {
				        $addon_dip_price_details['price'] = GLUTEN_PRICE ; 
			 }
                  }



                if (isset($addon_dip_price_details['price'])) {
                    $addon_dip_price = $addon_dip_price_details['price'];
                    $total+=$addon_dip_price;
                }
       
            }
           
        }

        if (isset($_POST['special']['addon_id'])) {

            $addon_id = $_POST['special']['addon_id'];
            $addon_spl_price_details = $Prices->find()
                    ->where(['Prices.product_id' => $product_id, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                    ->first();
            if (isset($addon_spl_price_details['price'])) {
                $addon_spl_price = $addon_spl_price_details['price'];
                //echo $addon_price; exit();


 
                $total+=$addon_spl_price;
            }
        }

        if (isset($_POST['crust'])) {
            $price = CRUST_PRICE;
            $crust = $_POST['crust'];
            if ($crust == "thick") {
                $total+=$price;
            }
        }

        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        echo($final);
        exit();

     
    }




    public function special_pizza_calculate_yours() {
        
        $delLoc  = 0;
        $delType = 'pickup';
        
        $delType = $this->request->session()->read('Config.deltype');
        $delLoc  = $this->request->session()->read('Config.location');
        
        
        $product_count  = $_POST['product_count'];
        $product_id     = $_POST['product_id'];
        $isCheck        = true;
        $initial_total  = $total = $_POST['start_price'];
        $size           = $_POST['size'];

        
            //////////// vaisakh add top count////////      
        $Addons         = TableRegistry::get('Addons');
        $addons    = $Addons->find();
        $addons_count       = array();               
        /*foreach($addons as $key1 => $value1)
            {
            
                $addons_count[$value1->id]= $value1->topping_count;
            }*/
        //////////// vaisakh add top count////////

        // $PriceRule      = TableRegistry::get('PricingRule');
        // $pricsArr       = $PriceRule->find()->where(['restaurants_id'=>$delLoc,'type'=>$delType, 'size'=>$size]);
        
        // $priceList      = array();
        // foreach($pricsArr as $key => $value)
        //     {
        //         $priceList[$value->topping_count] = $value->price;
        //     }    
        
        $ToppingPrice   = TableRegistry::get('ToppingPrice');
        $toppingsArr    = $ToppingPrice->find();    

        $flag           = 0;
        $toppings       = array();
        foreach($toppingsArr as $key => $value)
            {
                $flag++;
                if($flag==1)
                {
                    $id=$value->addon_id;
                }
                $toppings[$value->size][$value->type][$value->addon_id] = $value->price;
            }
                     
        $maxCount        = MAX_COUNT;
        $uptotops        = 0;
        $increment       = 0;
        $maketops        = 0;
        $firstarray = unserialize(DOUBLE_ID); 
        $defaulttopps   = $this->Products->find()->where(['id' => $product_id ])->first();  
        $defaultTopscnt = explode(',',$defaulttopps->default_toppings);
        $defdips        = explode(',',$defaulttopps->default_dips);
         
        $defaultsetPrice = $toppings[$size]['full'][$id];
        //$defaultsetPrice = $toppings[$size]['full'];

        foreach ($_POST['addon'] as $key => $main) 
        {
            foreach ($main as $key1 => $subcat) 
            {
                $addon_price_details['price']   = 0.00;
                $addon_id                       = $subcat['addon_id'];              
                
                if ($_POST['product_id'])
                {
                    $side = $subcat['side'];
                    if ($side == "left" || $side == "right") 
                    {
                        $side1 = "half";
                    } else 
                    {
                        $side1 = "full";
                    }

                    $type     = $subcat['type'];
                    
                    if($type!='' && $subcat['default']==0)
                    { 
                        
                        $topsingleCount = $type[0];
                        //$premium = $addons_count[$addon_id];
                        if(in_array($addon_id,$firstarray))
                            {
                                $premium = 2;
                            }
                            else
                            {
                                $premium = 1;
                            }
                        $topsingleCount = $topsingleCount*$premium;
                                
                        if($side=='full')
                        {
                            $uptotops += $topsingleCount;
                        }
                        else
                        {
                            $uptotops += 0.5 * $topsingleCount; 
                        }
                        if($side=='full')
                        {
                            $maketops += $topsingleCount;
                        }
                        else
                        {
                            $maketops += 0.5 * $topsingleCount; 
                        }
                        
                             
                        if($uptotops>$maxCount && $isCheck==true)
                            {
                                $topsingleCount = $uptotops-$maxCount;
                                if($side1=='half')
                                {
                                    $topsingleCount = $topsingleCount/0.5;
                                }
                                $isCheck      = false;
                                $addon_price_details['price'] =  $toppings[$size][$side1][$addon_id];
                            }
                        elseif($uptotops>$maxCount && $isCheck==false)
                            {
                                $addon_price_details['price']          =  $toppings[$size][$side1][$addon_id];
                            }

                             
                        if (isset($addon_price_details['price'])) 
                        {   
                            
                               $addon_price = $addon_price_details['price']*$topsingleCount; 
                               $total+=$addon_price;
                             
                            //echo '('.$addon_id.','.$addon_price.','.$total.')|';
                            //$total+=$addon_price;
                            //$isCheck  = true;
                                                        
                        }
                    }
                    
                }
            }
        }

        if (isset($_POST['dips'])) {
            foreach ($_POST['dips'] as $key => $value) {
                
                 /** dips[][addon_id] changd to dips[][addon_id][]  in speciality pizza **/

                  if(isset($_POST['product_combo_id']) && $_POST['product_combo_id'] !=''):
                       $addon_id = $value['addon_id'];
                       $_POST['tagcatid']=SAUCEID;
                  else:
                       $addon_id = $value['addon_id'][0]; 
                  endif;
                 
                $addon_dip_price_details['price'] = '';

                if($key != DOUGH_ID)
                {
                
                    if(isset($value['addon_id'][0]) && $value['addon_id'][0] != PIZZA_SAUCE_ID && $value['addon_id'][0] != NO_SAUCE_ID && !in_array($value['addon_id'][0],$defdips))
                         {
                            $addon_dip_price_details['price'] = $defaultsetPrice ;
                         }
                }
                else
                  {
                     if($value['addon_id'][0] == GLUTEN_ID)
                     {
                        $addon_dip_price_details['price'] = GLUTEN_PRICE ; 
             }
                  }



                if (isset($addon_dip_price_details['price'])) {
                    $addon_dip_price = $addon_dip_price_details['price'];
                    $total+=$addon_dip_price;
                }
       
            }
           
        }

        if (isset($_POST['special']['addon_id'])) {

            $addon_id = $_POST['special']['addon_id'];
            $addon_spl_price_details = $Prices->find()
                    ->where(['Prices.product_id' => $product_id, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                    ->first();
            if (isset($addon_spl_price_details['price'])) {
                $addon_spl_price = $addon_spl_price_details['price'];
                //echo $addon_price; exit();


 
                $total+=$addon_spl_price;
            }
        }

        if (isset($_POST['crust'])) {
            $price = CRUST_PRICE;
            $crust = $_POST['crust'];
            if ($crust == "thick") {
                $total+=$price;
            }
        }

        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        echo($final);
        exit();

     
    }




    public function pizza_calculate_appatizer() {
        
        $delLoc  = 0;
        $delType = 'pickup';
        
        $delType = $this->request->session()->read('Config.deltype');
        $delLoc  = $this->request->session()->read('Config.location');
        
        
        $product_count  = $_POST['product_count'];
        $product_id     = $_POST['product_id'];
        $isCheck        = true;
        $initial_total  = $total = $_POST['start_price'];
        $size           = $_POST['size'];

        
            //////////// vaisakh add top count////////      
        $Addons         = TableRegistry::get('Addons');
        $addons    = $Addons->find();
        $addons_count       = array();               
        /*foreach($addons as $key1 => $value1)
            {
            
                $addons_count[$value1->id]= $value1->topping_count;
            }*/
        //////////// vaisakh add top count////////

            
        
        $ToppingPrice   = TableRegistry::get('ToppingPrice');
        $toppingsArr    = $ToppingPrice->find();    

        $flag           = 0;
        $toppings       = array();
        foreach($toppingsArr as $key => $value)
            {
                $flag++;
                if($flag==1)
                {
                    $id=$value->addon_id;
                }
                $toppings[$value->size][$value->type][$value->addon_id] = $value->price;
            }
                     
        $maxCount        = 0;
        if($product_id == 770)
        {
            $maxCount        = 3;
        }
        $uptotops        = 0;
        $increment       = 0;
        $maketops        = 0;
        $firstarray = unserialize(DOUBLE_ID); 
        $defaulttopps   = $this->Products->find()->where(['id' => $product_id ])->first();  
        $defaultTopscnt = explode(',',$defaulttopps->default_toppings);
        $defdips        = explode(',',$defaulttopps->default_dips);

        if($defaulttopps->category_id == DAY_SPECIAL)
        {
            $maxCount = DAY_SPECIAL_TOPPS;
        }
        elseif($defaulttopps->category_id == PICKUP_AND_WALKIN)
        {
            $maxCount = $defaulttopps->default_count; 
        }
        else
        {
            if($defaulttopps->default_count != '')
            {
                $maxCount       = $defaulttopps->default_count;
            }
            else
            {
                $maxCount       = 0;
            }
        }
        
        $defaultsetPrice = $toppings[$size]['full'];

        foreach ($_POST['addon'] as $key => $main) 
        {
            foreach ($main as $key1 => $subcat) 
            {
                $addon_price_details['price']   = 0.00;
                $addon_id                       = $subcat['addon_id'];              
                
                if ($_POST['product_id'])
                {
                    $side = $subcat['side'];
                    if ($side == "left" || $side == "right") 
                    {
                        $side1 = "full";
                    } else 
                    {
                        $side1 = "full";
                    }

                    $type     = $subcat['type'];
                    
                    if($type!='' && $subcat['default']==0)
                    { 
                        
                        $topsingleCount = $type[0];
                        //$premium = $addons_count[$addon_id];
                        if(in_array($addon_id,$firstarray))
                            {
                                $premium = 2;
                            }
                            else
                            {
                                $premium = 1;
                            }
                        $topsingleCount = $topsingleCount*$premium;
                                
                        if($side=='full')
                        {
                            $uptotops += $topsingleCount;
                        }
                        else
                        {
                            $uptotops += 1 * $topsingleCount; 
                        }
                        if($side=='full')
                        {
                            $maketops += $topsingleCount;
                        }
                        else
                        {
                            $maketops += 1 * $topsingleCount; 
                        }
                        
                             
                        if($uptotops>$maxCount && $isCheck==true)
                            {
                                $topsingleCount = $uptotops-$maxCount;
                                if($side1=='half')
                                {
                                    $topsingleCount = $topsingleCount/1;
                                }
                                $isCheck      = false;
                                $addon_price_details['price'] =  $toppings[$size][$side1][$addon_id];
                            }
                        elseif($uptotops>$maxCount && $isCheck==false)
                            {
                                $addon_price_details['price']          =  $toppings[$size][$side1][$addon_id];
                            }

                             
                        if (isset($addon_price_details['price'])) 
                        {   
                            
                               $addon_price = $addon_price_details['price']*$topsingleCount; 
                               $total+=$addon_price;
                        
                            //echo '('.$addon_id.','.$addon_price.','.$total.')|';
                            //$total+=$addon_price;
                            //$isCheck  = true;
                                                        
                        }
                    }
                    
                }
            }
        }

        if (isset($_POST['dips'])) {
            foreach ($_POST['dips'] as $key => $value) {
                
                 /** dips[][addon_id] changd to dips[][addon_id][]  in speciality pizza **/

                  if(isset($_POST['product_combo_id']) && $_POST['product_combo_id'] !=''):
                       $addon_id = $value['addon_id'];
                       $_POST['tagcatid']=SAUCEID;
                  else:
                       $addon_id = $value['addon_id'][0]; 
                  endif;
                 
                $addon_dip_price_details['price'] = '';

                if($key != DOUGH_ID)
                {
                     
                 if(isset($value['addon_id'][0]) && !in_array($value['addon_id'][0],$defdips) && $value['addon_id'][0] != NO_SAUCE_ID)
                    {
                        
                        $addon_dip_price_details['price'] = $defaultsetPrice ; 
                    }
                }
                else
                  {
            if($value['addon_id'][0] == GLUTEN_ID)
            {
                $addon_dip_price_details['price'] = GLUTEN_PRICE ; 
            }
                  }



                if (isset($addon_dip_price_details['price'])) {
                    $addon_dip_price = $addon_dip_price_details['price'];
                    $total+=$addon_dip_price;
                }
       
            }
           
        }

        if (isset($_POST['special']['addon_id'])) {

            $addon_id = $_POST['special']['addon_id'];
            $addon_spl_price_details = $Prices->find()
                    ->where(['Prices.product_id' => $product_id, 'Prices.addon_id' => $addon_id, 'Prices.size' => $size])
                    ->first();
            if (isset($addon_spl_price_details['price'])) {
                $addon_spl_price = $addon_spl_price_details['price'];
                //echo $addon_price; exit();


 
                $total+=$addon_spl_price;
            }
        }

        if (isset($_POST['crust'])) {
            $price = CRUST_PRICE;
            $crust = $_POST['crust'];
            if ($crust == "thick") {
                $total+=$price;
            }
        }

        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        echo($final);
        exit();

     
    }
	
	
	/**
	 *@csp Jul-26
	 *for calculate start price make your own pizza
	 *return configuration details
	**/
	public function make_start_price() {
		
		$delLoc  = 0;
		$delType = 'pickup';
		
		$delType = $this->request->session()->read('Config.deltype');
		$delLoc  = $this->request->session()->read('Config.location');
		
		
        $product_count  = $_POST['product_count'];
        $product_id 	= $_POST['product_id'];
        $isCheck        = true;
		$initial_total  = $total = $_POST['start_price'];
        $size 			= $_POST['size'];
        
		$PriceRule   	= TableRegistry::get('PricingRule');
		$pricsArr  	    = $PriceRule->find()->where(['restaurants_id'=>$delLoc,'type'=>$delType, 'size'=>$size]);
		
		$priceList      = array();
		foreach($pricsArr as $key => $value)
			{
				$priceList[$value->topping_count] = $value->price;
			}
           
		$maxCount        = MAX_COUNT;
		$uptotops        = 0;
		$increment       = 0;
		
		$defaultsetPrice = $toppings[$size]['full'];
		

        foreach ($_POST['addon'] as $key => $main) 
		{
            foreach ($main as $key1 => $subcat) 
			{
				$addon_price_details['price']   = 0.00;
				
                if ($_POST['product_id'])
				{
                    $side = $subcat['side'];
                    if ($side == "left" || $side == "right") 
					{
                        $side1 = "full";
                    } else 
					{
                        $side1 = "full";
                    }

                    $type 	  = $subcat['type'];
                    $addon_id       = $subcat['addon_id'];
					
					if($type!='' && $addon_id!=MAKE_CHEESE_ID)
					{ 
						
						$topsingleCount = $type[0];
						
						if($side=='full')
						{
							$uptotops += $topsingleCount;
						}
						else
						{
							$uptotops += 1 * $topsingleCount; 
						}
						
						if($uptotops<=1)
						{
							$total = $priceList[1];
						}
						elseif($uptotops >1 && $uptotops <3)
						{
							$total = $priceList[2];
						}
						elseif($uptotops >=3)
						{
							$total = $priceList[3];
						}
							 
						
						
					}
                    
                }
            }
        }

        $final = $total ;

        $final = number_format((float) $final, 2, '.', '');
        echo($final);
        exit();

     
    }
	
	
	public function platter_price() {
        $maincategory_id = $_POST['maincategory_id'];

        $product_count = $_POST['product_count'];
        $product_id = $_POST['product_id'];
        $platter_count = $_POST['platter_count'];
        $sauce_count = $_POST['sauce_count'];
        $max_sauce_count = $_POST['max_sauce_count'];
		$total = $_POST['start_price'];
		

        $sizeCount      = 1;
        $sizeCounttotal = 0;
        $totalFlag      = True;


        $Combos = TableRegistry::get('Combos');
        $addon_price_details = $Combos->find()
                ->where(['Combos.type' => 'platter', 'Combos.count' => $platter_count])
                ->first();
        if (isset($addon_price_details['price'])) {
            $total = $addon_price_details['price'];
        }


        $i = 1;
        if (isset($_POST['addons'])) {
            foreach ($_POST['addons'] as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    if (isset($value1['addon_id'])) {

                         /*** for counting sauce after no. of defaults***/

                            $totsize     = $_POST['drinks_count'][$value1['addon_id']];
	                    if($totsize!='' && $totsize!=0):
	                       $sizeCount = $totsize;
	                    endif;
                            $sizeCounttotal += $sizeCount;
                          
                            if($sizeCounttotal>$max_sauce_count && $totalFlag == true)
                             {
                                 $sizeCount = $sizeCounttotal-$max_sauce_count;
                                 $totalFlag = false;
                             }
                              
                            /**********************/



                        //if ($i > $max_sauce_count) {
                        if ($sizeCounttotal > $max_sauce_count) {
                            //$addon_price = $value1['price'];

                            $addon_price = $value1['price']* $sizeCount+OTTAWA_DIP_DIFFRENCE;
                            $total+=$addon_price;
                        }


                        $i++;
                    }
                }
            }
        }






        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        print_r($final);
        exit();

        //print_r($_POST); exit();
    }
	
	
	/**
	 *@csp Jul-09
	 *for calculate addon price for panzer rotti configuraion
	 *return configuration details
	**/
	public function app_calculate() {
		
        $product_count  = $_POST['product_count'];
        $product_id 	= $_POST['product_id'];
        $isCheck        = false;
		$initial_total  = $total = $_POST['start_price'];
		$max_sauce_count = $_POST['max_sauce_count'];

        $size 			= $_POST['size'];
		$ToppingPrice 	= TableRegistry::get('ToppingPrice');
		$toppingsArr    = $ToppingPrice->find();	

        $toppings       = array();
        foreach($toppingsArr as $key => $value)
			{
				$toppings[$value->size][$value->type] = $value->price;
			}
 		
		$mediumprice = $toppings['medium']['full'];
		
		$maxCount = 0;
		$PANZER  = unserialize (PANZER_ROTTY_ID);
		if(isset($max_sauce_count) && $max_sauce_count!='')
		{
			$maxCount =$max_sauce_count;
		}

		$i 		= 1;
		
		
		$firstarray = unserialize(DOUBLE_ID);
		$trippleID  = TRIPPLE_ID;
		$topsingleCount = 0 ;
                $totaltops      = 0;
                $checkflag        = true;

        if (isset($_POST['addons'])) {
            foreach ($_POST['addons'] as $key => $value) 
			{
				foreach($value['addon_id'] as $v)
				{
					$topsingleCount = $value[$v]['type'][0];
					if(in_array($v,$firstarray))
					{
						$premium = 2;
						$topsingleCount = $topsingleCount*$premium;
					}
					elseif($v==$trippleID)
					{
						$premium = 3;
						$topsingleCount = $topsingleCount*$premium;
					}
					else
					{
						$premium = 1;
						$topsingleCount = $topsingleCount*$premium;
					}

					$totaltops += $topsingleCount;
					
                                        if($totaltops>$maxCount && $checkflag==true)
					{
						$topsingleCount  = $totaltops-$maxCount;
						$checkflag       = false;
						$addon_dip_price = $mediumprice;
					}
					elseif($totaltops>$maxCount && $checkflag==false)
					{
						$addon_dip_price = $mediumprice;
					}
					else
                                        {
						$addon_dip_price = 0;
                                        }
    					if($v ==CRUST_CHILLY)
					{
						$addon_dip_price  = 0;
					}
                                        $total+= ($addon_dip_price*$topsingleCount);
					
				}
			
            }
           
        }


        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        echo($final);
        exit();

     
    }

public function final_combo_update_cart($value='')
    {
      
       $session = $this->request->session();
       $cart_items=$session->read('Cartarray1');
       

       
       // echo '<pre>';print_r($cart_items);

       if($value!=''):

       	//array_push($cart_items['combo'][$value],$cart_items['demo'][$value]);
          $cart_items['combo'][$value] = $cart_items['demo'][$value];

       else:
          array_push($cart_items['combo'],$cart_items['demo'][0]);
       endif;
      
       $sessionData = $session->write('Cartarray1',$cart_items);

     
      echo "sucess";exit;

    }

 public function salad_total_price() {
        
        $maincategory_id = $_POST['maincategory_id'];
        $product_count   = $_POST['product_count'];
        $product_id      = $_POST['product_id'];
        $total           = 0;

	$salad_addons    = array();
	$salad_addons    = explode(',',$_POST['addon_id']);
       
        if (isset($_POST['psize'])) {
            $salad_size = $_POST['psize'];
        } else {
            $salad_size = "";
        }


        if ($maincategory_id == SALAD_ID) {
            if (isset($_POST['salad_size'])) {
                $green_price = "";
                $salad_size = $_POST['salad_size'];
                $salad_green_id = $_POST['salad_green_id'];
                $green_price = $_POST['salad_green'][$salad_green_id][$salad_size];
                $total+=$green_price;
            }
        }
        

        if (isset($_POST['addons'])) {
            foreach ($_POST['addons'] as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    if (isset($value1['addon_id'])) {

                        if ($salad_size == 'large') {
                            $total+= $value1['lprice'];
                        } 
                      
                    }
                }
            }
        }


                foreach ($salad_addons as $key1 => $value1) {
                    if (isset($value1)) {

                        if ($salad_size == 'large') {
                            $total+= $_POST['salad_addon'][$value1][$salad_size];
                        } 
                      
                    }
                }
            
       


        if($salad_size == 'large')
         {
		//$total+= COMBO_FAMILY;
		$final = COMBO_FAMILY;
         }
       // $final = $total;

        $final = number_format((float) $final, 2, '.', '');
        echo $final;
        exit();

    }
	
	
	/*** for quick adding items to cart  ***/
	
	public function quick_add() {

        $specialArr    = array();
        $add_cartArr   = array();
        $freeAddArr    = array();
        $dipsAddArr    = array();
        $emptyArr 	   = array();
        $quickArr 	   = array();
        $product_count = 1;
        $total_price   = $_POST['price'];
        $product_id    = $_POST['product_id'];
        $Products 	   = TableRegistry::get('Products');
        $pizza_product = $Products->find("all")->where(['Products.id' => $product_id])->first();
		
		
		$addonsArr  = array();
        $Addons 	= TableRegistry::get('Addons');
        $addons 	= $Addons->find()->all();
        foreach ($addons as $key => $value) {
            $addonsArr[$value['id']] = array(
                'id' => $value['id'],
                'name' => $value['name'],
                'addon_category_id' => $value['addon_category_id'],
                'image' => $value['image'],
                'price' => $value['price'],
            );
        }

        $addonCategoriesArr = array();
        $AddonCategories = TableRegistry::get('AddonCategories');
        $addonCategories = $AddonCategories->find()->all();
        foreach ($addonCategories as $key => $value) {
            $addonCategoriesArr[$value['id']] = $value['name'];
        }
		
		
		$ProductAddons  = TableRegistry::get('ProductAddons');
        $ProAddon   	= $ProductAddons->find()->where(['product_id'=>$product_id])->all();
		
		$proAddonArray  = array();
		
		foreach($ProAddon as $val)
		{
			$proAddonArray[] = $val->addon_id;
		}
		
		$categoryArray =  array();
		
		foreach($proAddonArray as $val)
		{
			$categoryArray[$addonsArr[$val]['addon_category_id']][] = $val;
		}
		

		
		 $i = 0;
		foreach($categoryArray as $key => $padonArr)
		{
			$dipsAddArr1 					= array();
			foreach ($padonArr as $key1 => $value) 
			{
				$dipsAddArr1[]['addonnames'] = array('addon_id' => $value, 'name' => $addonsArr[$value]['name'], 'image' => $addonsArr[$value]['image'], 'price' => 0.00);
            }
			
			$addcatArr[$i]['addon_cat'] 		= $addonsArr[$value]['addon_category_id'];
			$addcatArr[$i]['addon_catname']     = $addonCategoriesArr[$key];	
			$addcatArr[$i]['addon_subcat'] 	    = $dipsAddArr1;
			
			$i++;
		}
		//echo '<pre>';print_r($addcatArr);exit;
		$addonArr = array('addons' => $addcatArr);

		$addonArr['addon_category_id'] = '';
		$addonArr['final_price'] 	   = $total_price;
		$addonArr['inital_price'] 	   = $total_price;
		$addonArr['product_image']	   = $pizza_product->image;
		$addonArr['product_name'] 	   = $pizza_product->name;
		$addonArr['product_id'] 	   = $product_id;
		$addonArr['subcategory_name']  = '';
		$addonArr['subcategory_id']    = '';
		$addonArr['maincategory_id']   = $pizza_product->main_category_id;
		$addonArr['size'] 			   = '';
		$addonArr['product_type']      = '';
		$addonArr['product_type_count'] = '';
		$addonArr['product_count']     = 1;
		$addonArr['sauce_instruction'] = '';
		$addonArr['instruction']       = '';
		
		
		$session = $this->request->session();
        if (!$session->read('Cartarray1')) 
		{
            $cartArr1 = array(
                'custom' => $emptyArr,
                'direct' => $emptyArr,
                'pizza' => $emptyArr,
                'quick' => $quickArr,
                'combo' => $emptyArr,
                 'demo'=>$emptyArr
            );
            $session = new Session();
            $sessionData = $session->write('Cartarray1', $cartArr1);
        }
		
		$session 	= $this->request->session();
        $cart_items = $session->read('Cartarray1');
        array_push($cart_items['custom'], $addonArr);
        $sessionData = $session->write('Cartarray1', $cart_items);
		
		$src1 = $this->request->webroot;
		$src = $src1 . "products/" . $pizza_product->image;
		
		$s = "<div class='added_component_box'><img  height='44px;' class='cart_img' width='44px;' src='" . $src . "'>
			<h3>" . $pizza_product->name . "</h3><div class='added_price'>Price : $" . $total_price . "</div>
			<div class='count_box'>" . $product_count . "</div>";

        echo $s;
		exit();
        
    }

/*** Sauce N Side Dish Configuration Section  ***/

/**
	 *@csp Jul-09
	 *for calculate addon price for panzer rotti configuraion
	 *return configuration details
	**/
public function combo_lasagana_calculate() {
		
        $product_count  = $_POST['product_count'];
        $product_id 	= $_POST['product_id'];
        $isCheck        = false;
	$initial_total  = $total = 0;
	$max_sauce_count = 0;
	$ToppingPrice 	= TableRegistry::get('ToppingPrice');
	$toppingsArr    = $ToppingPrice->find();	

        $toppings       = array();
        foreach($toppingsArr as $key => $value)
			{
				$toppings[$value->size][$value->type] = $value->price;
			}
 		
		$mediumprice = $toppings['medium']['full'];
		
		$maxCount = 0;
		
		
		$firstarray = unserialize(DOUBLE_ID);
		$trippleID  = TRIPPLE_ID;
		$topsingleCount = 0 ;
                $totaltops      = 0;
                $checkflag        = true;

        if (isset($_POST['addons'])) {
            foreach ($_POST['addons'] as $key => $value) 
			{
				foreach($value['addon_id'] as $v)
				{
					$topsingleCount = $value[$v]['type'][0];
					if(in_array($v,$firstarray))
					{
						$premium = 2;
						$topsingleCount = $topsingleCount*$premium;
					}
					elseif($v==$trippleID)
					{
						$premium = 3;
						$topsingleCount = $topsingleCount*$premium;
					}
					else
					{
						$premium = 1;
						$topsingleCount = $topsingleCount*$premium;
					}

					$totaltops += $topsingleCount;
					
                                        if($totaltops>$maxCount && $checkflag==true)
					{
						$topsingleCount  = $totaltops-$maxCount;
						$checkflag       = false;
						$addon_dip_price = $mediumprice;
					}
					elseif($totaltops>$maxCount && $checkflag==false)
					{
						$addon_dip_price = $mediumprice;
					}
					else
                                        {
						$addon_dip_price = 0;
                                        }
    					if($v ==CRUST_CHILLY)
					{
						$addon_dip_price  = 0;
					}
                                        $total+= ($addon_dip_price*$topsingleCount);
					
				}
			
            }
           
        }


        $final = $total * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        echo($final);
        exit();

     
    }
	
	
	/**** for testing  ***/
	 public function test_details($id = null) {
       
   	    $product_addons_list = array();
        $ProductAddons = TableRegistry::get('ProductAddons');
        $product_addons = $ProductAddons->find()
                ->where(['ProductAddons.product_id' => $id])
                ->all();
				
        foreach ($product_addons as $key => $value) {
            $product_addons_list[] = $value['addon_id'];
        }
        $Addons = TableRegistry::get('Addons');


        $addons_all = $Addons->find()
                ->where(['Addons.id IN' => $product_addons_list])
                ->order(['Addons.order' => asc])
                ->all();

        $this->set('addons_all', $addons_all);


        $addon_catArr = array();
		$addon_selection = array();
        $AddonCategories = TableRegistry::get('AddonCategories');

        $addon_cat = $AddonCategories->find("all");
		
		
		$addon_sub_cat = $AddonCategories->find()->all();
		$subcat_list   = array();
		foreach($addon_sub_cat as $sl_list)
		{
			$subcat_list[$sl_list->parent_id][] = $sl_list->id;
		}
		
		$this->set('subcat_list', $subcat_list);

        foreach ($addon_cat as $key => $value) {
            $addon_catArr[$value['id']] = $value['name'];
			$addon_selection[$value['id']] = $value;
        }

        $selection = $AddonCategories->find()
                ->where(['AddonCategories.selection' => "single"])
                ->all();
        $this->set('selection', $selection);


        $addons = $Addons->find()
                ->order(['Addons.order' => asc])
                ->all();
        $this->set('addons', $addons);
		$this->set('addon_selection', $addon_selection);
        $this->set('addon_catArr', $addon_catArr);
        $product = $this->Products->get($id, [
            'contain' => ['Categories', 'Addons']
        ]);

        // echo $product['category_id'];exit;

        if ((BASKET_ID == $product['category_id'])) {
            $addons_side = $Addons->find()
                    ->where(['Addons.addon_category_id ' => BSIDE_ID])
                    ->all();
            $this->set('addons_side', $addons_side);
        }


        $this->set('product', $product);
        $this->set('_serialize', ['product']);

        if (isset($_GET['key'])) {
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['custom'][$key];
            //echo "<pre>";print_r($selected_items); exit;
            $this->set('selected_items', $selected_items);
            $this->set('session_key', $key);

//        unset($cart_items['pizza'][$key]);
//        $session->delete('Cartarray1');
//        $session = new Session();
//        $sessionData = $session->write('Cartarray1',$cart_items);
        }
    }
	
	/**** NEW SECTION FOR CART  ***/
	
	 public function item_details($id = null) 
	 {
		$Addons     	 = TableRegistry::get('Addons'); 
		$AddonCategories = TableRegistry::get('AddonCategories');
		
		$product 	= $this->Products->get($id, ['contain' => ['Categories', 'Addons']]);
		
		$product_addons_list = $this->products_addon_list($id);
		$addons_all          = $this->get_addon_details($product_addons_list);
		
		list($addon_catArr, $addon_selection) =	$this->get_addon_categories();
		
		$subcat_list         =  $this->get_addon_subcategories();
		
        $addons = $Addons->find()->order(['Addons.order' => asc])->all();
		
		$product_price       =  $this->get_product_price($product,$product_addons_list);
		
		
		$product_size_cat   =  $this->get_size_category();
		
		$product_direct_cat =  $this->get_product_direct_cat($product_addons_list);
		
		$addon_orders       =  $this->get_addon_order($id);
		
		
		
		if(!empty($product_size_cat))
		{
			$product_direct_cat[] = $product_size_cat;
		}
		

        if (isset($_GET['key'])) 
		{
            $key = $_GET['key'];
            $session = $this->request->session();
            $cart_items = $session->read('Cartarray1');
            $selected_items = $cart_items['custom'][$key];
            $this->set('selected_items', $selected_items);
            $this->set('session_key', $key);
			
			$product_price       =  $selected_items['final_price'];
        }
		
		$this->set('product', $product);
		$this->set('addons_all', $addons_all);
		$this->set('addons', $addons);
		$this->set('addon_selection', $addon_selection);
        $this->set('addon_catArr', $addon_catArr);
		$this->set('subcat_list', $subcat_list);
		$this->set('product_price', $product_price);
		$this->set('product_direct_cat', $product_direct_cat);
		$this->set('addon_orders', $addon_orders);
        $this->set('_serialize', ['product']);
    }
	
	/**** for new cart and customization  ****/
	
	/**
	 *@csp Jan-03
	 *for getting addons for a product
	 *return array of addons
	**/
    private function products_addon_list($id) 
	{
		$product_addons_list = array();
        $ProductAddons 		 = TableRegistry::get('ProductAddons');
        $product_addons 	 = $ProductAddons->find()->where(['ProductAddons.product_id' => $id])->all();
        foreach ($product_addons as $key => $value) 
		{
           $product_addons_list[] = $value['addon_id'];
        }
		return $product_addons_list;
    }
	
	/**
	 *@csp Jan-27
	 *for getting addons details  for a product
	 *return array of addons
	**/
    private function get_addon_order($id) 
	{
		$order_arr  = array();
		$Addons     = TableRegistry::get('AddonOrders');
        $addons_all = $Addons->find()->where(['product_id' => $id])->order(['AddonOrders.orders' => asc])->all();
		foreach($addons_all as $all)
		{
			$order_arr[] = $all->addon_category_id;
		}
		return $order_arr;
    }
	
	/**
	 *@csp Jan-03
	 *for getting addons details  for a product
	 *return array of addons
	**/
    private function get_addon_details($product_addons_list) 
	{
		$Addons     = TableRegistry::get('Addons');
        $addons_all = $Addons->find()->where(['Addons.id IN' => $product_addons_list])->order(['Addons.id' => asc])->all();
		return $addons_all;
    }
	
	/**
	 *@csp Jan-03
	 *for getting addons for a product
	 *return array of addons
	**/
    private function get_addon_categories() 
	{
		$addon_catArr    = array();
		$addon_selection = array();
        $AddonCategories = TableRegistry::get('AddonCategories');
		$addon_cat 		 = $AddonCategories->find("all");
		
		foreach ($addon_cat as $key => $value) 
		{
            $addon_catArr[$value['id']]    = $value['name'];
			$addon_selection[$value['id']] = $value;
        }
		
		$selection_array = array($addon_catArr,$addon_selection);
		return $selection_array ;

    }
	
	/**
	 *@csp Jan-03
	 *for getting addon sub categories
	 *return array of addons
	**/
    private function get_addon_subcategories() 
	{
        $AddonCategories = TableRegistry::get('AddonCategories');
	    
		$addon_sub_cat   = $AddonCategories->find()->all();
		$subcat_list     = array();
		foreach($addon_sub_cat as $sl_list)
		{
			$subcat_list[$sl_list->parent_id][] = $sl_list->id;
		}
		return $subcat_list ;

    }
	
	/**
	 *@csp Jan-03
	 *for getting addon sub categories
	 *return array of addons
	**/
    private function get_product_price($product,$product_addons_list,$direct_id=null) 
	{ 
		$Addons             = TableRegistry::get('Addons');
		$ProductAddons      = TableRegistry::get('ProductAddons');
		$AddonPrices        = TableRegistry::get('AddonPrices');
		
		$product_base_price =  0;
		
		$delType 			=  $this->request->session()->read('Config.deltype');
        $default_addons     =  explode(",",$product->default_addons);
		$product_addons_cat =  $this->get_product_addon_cat($product_addons_list);
		
		$product_size_cat   =  $this->get_size_category();
		
		$product_direct_cat =  $this->get_product_direct_cat($product_addons_list);
		
       
		if(in_array($product_size_cat,$product_addons_cat))
		{
			
			if($direct_id!='')
			{
				$size_id        = $direct_id;
			}
			else
			{
				$size_all       = $Addons->find()->where(['Addons.id IN' => $product_addons_list,'addon_category_id'=>$product_size_cat])->order(['Addons.id' => asc])->first();
				$size_id        = $size_all->id;
			
			}
			
			$addon_price    = $AddonPrices->find()->where(['product_id' => $product->id,'addon_id'=>$size_id,'type'=>$delType])->first();
			
			
			$product_base_price += $addon_price->price ;
		}
		else
		{
			$product_base_price = $product->base_price;
		}
		
		
      return $product_base_price;
		
		
    }
	
	/**
	 *@csp Jan-03
	 *for getting addons categories  for a product
	 *return array of addons
	**/
    private function get_product_addon_cat($product_addons_list) 
	{
		$Addons     = TableRegistry::get('Addons');
        $addons_all = $Addons->find()->where(['Addons.id IN' => $product_addons_list])->order(['Addons.order' => asc])->all();
		$addon_cat  = array();
		
		foreach($addons_all as $all)
		{
			$addon_cats[] = $all->addon_category_id;
			$addon_cat    = array_unique($addon_cats);
		}
		
		return $addon_cat ; 
    }
	
	
	/**
	 *@csp Jan-03
	 *for getting size category in addons for a product
	 *return array of addons
	**/
    private function get_size_category() 
	{
		$addon_catArr    = array();
        $AddonCategories = TableRegistry::get('AddonCategories');
		$addon_cat 		 = $AddonCategories->find()->where(['is_size'=>1])->first();
		
	
		return $addon_cat->id ;

    }
	
	/**
	 *@csp Jan-03
	 *for getting size category in addons for a product
	 *return array of addons
	**/
    private function get_product_direct_cat() 
	{
		$addon_catArr    = array();
        $AddonCategories = TableRegistry::get('AddonCategories');
		$addon_cat 		 = $AddonCategories->find()->where(['is_size'=>0,'id IN'=>$product_addons_list,'direct_pricing'=>1])->all();
		
		foreach ($addon_cat as $key => $value) 
		{ 
            $addon_catArr[]    = $value['id'];
        }		
		return $addon_catArr ;

    }
	
	/**
	 *@csp Jan-03
	 *for getting no of free category in addons for a product
	 *return array of addons
	**/
    private function _GetFreeAddonList($id) 
	{
		$addon_freeArr    = array();
        $FreeAddons       = TableRegistry::get('FreeAddons');
		$addon_cat 		  = $FreeAddons->find()->where(['product_id'=>$id])->all();
		
		foreach ($addon_cat as $key => $value) 
		{   
            $addon_freeArr[$value->addon_category_id]    = $value->free_addons;
        }		
		return $addon_freeArr ;

    }
	
	/**
	 *@csp Jan-03
	 *for getting price of addons for a product
	 *return array of addons
	**/
    private function _GetAddonPrice($id) 
	{
		$delType 		  = $this->request->session()->read('Config.deltype');
		$addon_priceArr   = array();
        $AddonPrices      = TableRegistry::get('AddonPrices');
		$addon_cat 		  = $AddonPrices->find()->where(['product_id'=>$id,'type'=>$delType])->all();
		
		foreach ($addon_cat as $key => $value) 
		{   
            $addon_priceArr[$value->addon_id]    = $value->price;
        }		
		return $addon_priceArr ;

    }
	
	/**
	 *@csp Jan-03
	 *for calculating the 
	 *return array of addons
	**/
	
	public function calculate_total_price() 
	{
       
	    $this->layout    = false;
		
        $sauce_count     = $_POST['sauce_count'];
        $max_sauce_count = $_POST['max_sauce_count'];
        $maincategory_id = $_POST['maincategory_id'];

        $sizeCount       = 1;
        $sizeCounttotal  = 0;
        $totalFlag       = True;
		$product_price   = 0;

        $product_count   = $_POST['product_count'];
        $product_id      = $_POST['product_id'];
		$direct_id       = '';
		if(isset($_POST['direct_addons']))
		{
			$direct_id = $_POST['direct_addons'];
		}
		
		$product 	         = $this->Products->get($product_id, ['contain' => ['Categories', 'Addons']]);
		$product_addons_list = $this->products_addon_list($product_id);
		$get_free_addon      = $this->_GetFreeAddonList($product_id);		
		$get_addon_price     = $this->_GetAddonPrice($product_id);
	    $product_price       = $this->get_product_price($product,$product_addons_list,$direct_id);
		$default_addons      = explode(",",$product->default_addons);
		
		 if (isset($_POST['addons']))
		 {   
			 foreach ($_POST['addons'] as $key => $value)
			 {
				$addon_cnt = 1;
				 foreach ($value as $key1 => $value1) 
				 {
					 
					 if (isset($value1['addon_id']))
					 {
						  
						 if($addon_cnt > $get_free_addon[$key] )
						 {
							 if(!in_array($value1['addon_id'],$default_addons ))
							 {
								 $product_price+= $get_addon_price[$value1['addon_id']];
								
							 }							 
						 }
						 $addon_cnt++;
					 }
				 }
			 }
		 }
		
		

        $final = $product_price * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        print_r($final);
        exit();

    }
	
	
	/*** for new cart function   ***/
	public function create_cart() 
	{
        $size_id		   = "";
        $size	 		   = "";
        $addonsArr 		   = array();
        $dipsauceFlavour   = array();
		$product_count     = 1;
		$total             = 0;
		$sauce_instruction = "";
		$maincategory_id   = $_POST['maincategory_id'];
		/** for arranging addons as an array **/
		
		$product 			 = $this->Products->get($_POST['product_id'], ['contain' => ['Categories', 'Addons']]);		
		$product_addons_list = $this->products_addon_list($_POST['product_id']);
		
        $Addons = TableRegistry::get('Addons');
        $addons = $Addons->find()->all();
        foreach ($addons as $key => $value) 
		{
            $addonsArr[$value['id']] = array(
												'id' => $value['id'],
												'name' => $value['name'],
												'addon_category_id' => $value['addon_category_id'],
												'image' => $value['image'],
												'price' => $value['price'],
											);
		}
		
		
		
		/*** for setting addon categories  **/
        $AddonCategories = TableRegistry::get('AddonCategories');
        $addon_cat = $AddonCategories->find("all");
        foreach ($addon_cat as $key => $value) 
		{
            $addon_catArr[$value['id']] = $value['name'];
        }
		
		/** for setting the size otr type of product  **/
		$direct_id       = '';
		if(isset($_POST['direct_addons']))
		{
			$direct_id = $_POST['direct_addons'];
			$size      = $addonsArr[$direct_id]['name'];
		}
		
		/*** for finding total price  ***/
		if ($maincategory_id != 53 && $maincategory_id != DIPPINGSAUCECATEGORY_ID)
		 {			
			 $total = $this->_CartTotalPrice();
			 $maincategory_id 	= $_POST['maincategory_id'];
             $inital_price 		= $this->get_product_price($product,$product_addons_list,$direct_id);
		 }
		elseif($maincategory_id == DRINK_ID)
		{
			
			$total 			 = $_POST['total_price'];
			
			$maincategory_id = $_POST['maincategory_id'];
            $inital_price 	 = $_POST['start_price'];

		}
		elseif($maincategory_id == DIPPINGSAUCECATEGORY_ID)
		{
			$total 			= $_POST['total_price'];
			
			$maincategory_id = $_POST['maincategory_id'];
            $inital_price    = $_POST['start_price'];
		}
		else
		{
			
			$total = $_POST['final_price'];
			
			$maincategory_id = $_POST['maincategory_id'];
            $inital_price    = $_POST['start_price'];
		}
		
		
        /*** for setting product count  **/
		
		if (isset($_POST['product_count']) && $_POST['product_count'] != "") 
		{
            $product_count = $_POST['product_count'];
        }
        
		/*** for sauce instruction  **/
		
        
        if (isset($_POST['sauce_instruction'])) 
		{			
            $sauce_instruction = $_POST['sauce_instruction'];
        }
		
     

        $product_id 	= $_POST['product_id'];
        $product_name 	= $_POST['product_name'];
        $product_image 	= $_POST['product_image'];
		
        if ($product_image == "") 
		{
            $product_image = "no_image1.png";
        }
		
        $subcategory_id 	= "";
        $subcategory_name 	= "";
        $product_type 		= "";
        $green_name 		= "";
        $sauce_option 		= "";

        if ($maincategory_id == SALAD_ID) 
		{
            $sauce_instruction  = "";
            $product_type 		= $_POST['salad_type'];           
            $green 				= explode(",", $_POST['green_id']);
			$greenarray 		= array();
		 
			if(count($green)>0)
			 {
				 foreach($green as $v)
				{
					$greenarray[] = array('addon_id' => $v, 'name' => $_POST['salad_green'][$v]['name'],'price' => $_POST['salad_green'][$v][$size]);
				}
			 }
			 
			$green_name = $greenarray;			
            $dressname 	= array();
			
			if($_POST['dress_id']!='')
			{
				$dressname[] = array('addon_id' => $_POST['dress_id'], 'name' => $_POST['dress_name'],'price' => 0.00);
			}

			$add_on 		= explode(",", $_POST['addon_id']);
			$add_onarray 	= array();
		 
		   if(count($add_on)>0)
		   {
			 foreach($add_on as $v)
				{																			   
					$add_onarray[] = array('addon_id' => $v, 'name' => $_POST['salad_addon'][$v]['name'],'price' => $_POST['salad_addon'][$v][$size]);
				}
		   }                                                             
        }
		
		/** for drinks add to cart  **/
        $drinksFlavour		=	array();
		$drinksFlavourPrice =   array();
		
        if ($maincategory_id == DRINK_ID) 
		{
            $product_type_id = $_POST['drinks_type'];
            foreach ($product_type_id as $key=>$value)
            {
                $count				   = $_POST['drinks_count_'.$value];
                $product_typeArr[]     = $count." ".$_POST['drinkTypeid_'.$value];
                $drinksFlavour[$value] = $count;
				
				$drinking_price 	   = $_POST['drinks_price_'.$value];
				$drinksFlavourPrice[$value]=$drinking_price;
            }
            
            $product_type	=	implode(',',$product_typeArr);
            $size_id 		= 	$_POST['drinks_size'];
            $size 			= 	$_POST['drinkSize_'.$size_id];
        }
		
		
		/** for dipping sauce add to cart  **/
		
        $dipsFlavour	=	array();
		$dipsPrice  	=	array();
        if ($maincategory_id == DIPPINGSAUCECATEGORY_ID) 
		{
            $product_type_id = $_POST['dips_type'];
            foreach ($product_type_id as $key=>$value)
            {
                $count				= $_POST['drinks_count_'.$value];
                $product_typeArr[]  = $count." ".$_POST['drinkTypeid_'.$value];
                $dipsFlavour[$value]= $count;
				
				$dipping_price 		= $_POST['drinks_price_'.$value];
				$dipsPrice[$value]	= $dipping_price;				
            }          
            $product_type=implode(',',$product_typeArr);           
        }

		/** for additional customer instruction  **/
        $instruction   = $_POST['instruction'];

		/** setting the session for cart **/
		$session = $this->request->session();
      
        if (!$session->read('Cartarray1')) 
		{

            $emptyArr    = array();
            $emptyArr1   = array();

            $cartArr1    = array('custom' => $emptyArr, 'direct' => $emptyArr, 'pizza' => $emptyArr, 'combo' => $emptyArr, 'quick' => $quickArr, 'demo'=>$emptyArr);
            $session     = new Session();
            $sessionData = $session->write('Cartarray1', $cartArr1);
        }

        $addcatArr 		= array();
        $add_cartArr 	= array();
        $aaddArr 		= array();
        $i 				= 0;
		
		$get_free_addon      = $this->_GetFreeAddonList($product_id);		
		$get_addon_price     = $this->_GetAddonPrice($product_id);
		$default_addons      = explode(",",$product->default_addons);
	
        if (isset($_POST['addons']) || ($maincategory_id == DRINK_ID) || ($maincategory_id == DIPPINGSAUCECATEGORY_ID)) 
		{
		 
          foreach ($_POST['addons'] as $key => $value) 
		  {
			$addon_cnt = 1;
            $k = 0;
            $dipsAddArr1 = array();
			$addcatArr[$i]['addon_cat'] = $key;
			$addcatArr[$i]['addon_catname'] = $addon_catArr[$key];

			foreach ($value as $key1 => $value1) 
			{ 
				if (isset($value1['addon_id'])) 
				{
					 if($addon_cnt > $get_free_addon[$key] )
					 {
						 if(!in_array($value1['addon_id'],$default_addons ))
						 {
							 $pricecart = $get_addon_price[$value1['addon_id']];
							
						 }
						 else
						 {
							  $pricecart = 0.00;
						 }
					 }
					 else
					 {
						 $pricecart= 0.00;
					 }
					 
					 $addon_cnt++;

					$dipsAddArr1[]['addonnames'] = array('addon_id' => $value1['addon_id'], 'name' => $addonsArr[$value1['addon_id']]['name'], 'image' => $addonsArr[$value1['addon_id']]['image'], 'price' => $pricecart);
				}				
			} 
									 				 
			$addcatArr[$i]['addon_subcat'] = $dipsAddArr1;
			$i++;
          }

            $i 		  = 0;
            $car 	  = array();
            $addonArr = array('addons' => $addcatArr);

            $addonArr['addon_category_id']   = $addon_category_id;
            $addonArr['final_price'] 		 = $total;
            $addonArr['inital_price'] 		 = $inital_price;
            $addonArr['product_image'] 		 = $product_image;
            $addonArr['product_name'] 		 = $product_name;
            $addonArr['product_id'] 		 = $product_id;
            $addonArr['subcategory_name']    = $subcategory_name;
            $addonArr['subcategory_id'] 	 = $subcategory_id;
            $addonArr['maincategory_id'] 	 = $maincategory_id;
            $addonArr['size'] 				 = $size;
            $addonArr['product_type'] 		 = $product_type;
             $addonArr['product_type_count'] = $product_type_countArr;
            $addonArr['product_count'] 		 = $product_count;
            $addonArr['sauce_instruction'] 	 = $sauce_instruction;
            $addonArr['instruction'] 		 = $instruction;
            $addonArr['green_name'] 		 = $green_name;
            $addonArr['pizza_count'] 		 = $pizza_count;
            $addonArr['size_id'] 			 = $size_id;
            $addonArr['drinksFlavour'] 		 = $drinksFlavour;
            $addonArr['dipsFlavour'] 		 = $dipsFlavour;
			$addonArr['dressname'] 			 = $dressname;
			$addonArr['add_on']				 = $add_onarray;
			$addonArr['popsFlavour'] 		 = $popsFlavour;
            $addonArr['dipsauceFlavour'] 	 = $dipsauceFlavour;			
			$addonArr['drinksFlvPrice']		 = $drinksFlavourPrice;
			$addonArr['popsFlvPrice'] 		 = $popsFlavourPrice;
			$addonArr['dipsFlvPrice'] 		 = $dipsPrice;
			$addonArr['sub_product'] 		 = $burgerArr;
			$addonArr['sauce_option'] 		 = $sauce_option;

            $cart_items = array();
            $session 	= $this->request->session();
            if ($session->read('Cartarray1')) 
			{
                $cart_items = $session->read('Cartarray1');
                $pizza_key  = $_GET['key'];
                if ($pizza_key != "") 
				{
                    unset($cart_items['custom'][$pizza_key]);
                    $session->delete('Cartarray1');
                    $session 	 = new Session();
                    $sessionData = $session->write('Cartarray1', $cart_items);
                }
                $session 	= $this->request->session();
                $cart_items = $session->read('Cartarray1');
				
                array_push($cart_items['custom'], $addonArr);
				
                $sessionData = $session->write('Cartarray1', $cart_items);
            } 
			else 
			{		
                $k 		  = array();
                $cartArr  = array($addcatArr);
                $cartArr1 = array('custom' => $cartArr, 'direct' => $k);
                $session     = new Session();
                $sessionData = $session->write('Cartarray1', $cartArr1);
            }

            $src1 = $this->request->webroot;
            $src = $src1 . "products/" . $product_image;

            $s = "<div class='added_component_box'><img  height='44px;' class='cart_img' width='44px;' src='" . $src . "'>
      <h3>" . $size . $product_name . "</h3><div class='added_price'>Price : $" . $total . "</div>
      <div class='count_box'>" . $product_count . "</div>";
            echo $s;
            exit;
          
        }

		else
		{
			$maincategory_id 	= $_POST['maincategory_id'];
			$maincategory_name  = $_POST['maincategory_name'];

			$base_price 		= $_POST['product_baseprice'];
			$total_price 		= $_POST['total_price'];

			$directArr = array(
				'maincategory_id' => $maincategory_id,
				'maincategory_name' => $maincategory_name,
				'product_count' => $product_count,
				'product_id' => $product_id,
				'product_name' => $product_name,
				'product_image' => $product_image,
				'base_price' => $base_price,
				'total_price' => $total_price,
				'subcategory_id' => $subcategory_id,
				'subcategory_name' => $subcategory_name,
				'size' => $size
			);

            $session 		= $this->request->session();
            $cart_items 	= $session->read('Cartarray1');
            array_push($cart_items['direct'], $directArr);
            $sessionData 	= $session->write('Cartarray1', $cart_items);

            $src1 = $this->request->webroot;
            $src = $src1 . "products/" . $product_image;

            $s = "<div class='added_component_box'><img  height='44px;' class='cart_img' width='44px;' src='" . $src . "'>
      <h3>" . $size . $product_name . "</h3><div class='added_price'>Price : $" . $total . "</div>
    <div class='count_box'>" . $product_count . "</div>";
            echo $s;
            exit;
        }

    }
	
	/*** for returninig the total cart price   **/
	
	public function _CartTotalPrice() 
	{
       
	    $this->layout    = false;
		
        $sauce_count     = $_POST['sauce_count'];
        $max_sauce_count = $_POST['max_sauce_count'];
        $maincategory_id = $_POST['maincategory_id'];

        $sizeCount       = 1;
        $sizeCounttotal  = 0;
        $totalFlag       = True;
		$product_price   = 0;

        $product_count   = $_POST['product_count'];
        $product_id      = $_POST['product_id'];
		$direct_id       = '';
		if(isset($_POST['direct_addons']))
		{
			$direct_id = $_POST['direct_addons'];
		}
		
		$product 	         = $this->Products->get($product_id, ['contain' => ['Categories', 'Addons']]);
		$product_addons_list = $this->products_addon_list($product_id);
		$get_free_addon      = $this->_GetFreeAddonList($product_id);		
		$get_addon_price     = $this->_GetAddonPrice($product_id);
	    $product_price       = $this->get_product_price($product,$product_addons_list,$direct_id);
		$default_addons      = explode(",",$product->default_addons);
		
		 if (isset($_POST['addons']))
		 {   
			 foreach ($_POST['addons'] as $key => $value)
			 {
				$addon_cnt = 1;
				 foreach ($value as $key1 => $value1) 
				 {
					  
					 if (isset($value1['addon_id']))
					 {
						  
						 if($addon_cnt > $get_free_addon[$key] )
						 {
							 if(!in_array($value1['addon_id'],$default_addons ))
							 {
								 $product_price+= $get_addon_price[$value1['addon_id']];
								
							 }							 
						 }
						 $addon_cnt++;
					 }
				 }
			 }
		 }
		
		

        $final = $product_price * $product_count;

        $final = number_format((float) $final, 2, '.', '');
        return $final;

    }

  
}
