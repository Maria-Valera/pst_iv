<!DOCTYPE html>
<html lang="en">
<?php $this->GetHeader("SGSC | UNEFA");?>
<body
	x-data="{ page: 'signin', 'loaded': true, 'darkMode': true, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }"
	x-init="
          darkMode = JSON.parse(localStorage.getItem('darkMode'));
          $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))"
	:class="{'dark text-bodydark bg-boxdark-2': darkMode === true}">

	<!-- ===== Page Wrapper Start ===== -->
	<div class="flex h-screen overflow-hidden">
		<?php $this->GetComplement('sidebar_menu');?>
		<!-- ===== Content Area Start ===== -->
		<div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
		<?php $this->GetComplement('header');?>
      <main>
        <div class="max-w-screen-2xl mx-auto p-4 md:p-6 2xl:p-10">
        <?php 
          $this->GetComplement('breadcrumb',['title_breadcrumb' => "Modulo Carrera"]);
        ?>
          <!-- ====== Form Layout Section Start -->   
          <div class="grid grid-cols-1 gap-9 sm:grid-cols-1">
            <div class="flex flex-col gap-9">
              <!-- Contact Form -->
              <div
                class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
                  <h3 class="font-semibold text-black dark:text-white">
                    Contact Form
                  </h3>
                </div>
                <form action="<?php $this->SetURL('controllers/carrera_controller.php');?>" autocomplete="off" method="POST">
                  <input type="hidden" name="ope" value="Registrar">
                  <div class="p-6.5">
                    <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                      <div class="w-full xl:w-2/6">
                        <label class="mb-2.5 block text-black dark:text-white">
                          Codigo de carrera <span class="text-meta-1">*</span>
                        </label>
                        <input type="text" placeholder="Enter your first name" name="codigo_carrera"
                          class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
                      </div>

                      <div class="w-full xl:w-2/6">
                        <label class="mb-2.5 block text-black dark:text-white">
                          Nombre de la carrera <span class="text-meta-1">*</span>
                        </label>
                        <input type="text" placeholder="Enter your last name" name="nombre_carrera"
                          class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:focus:border-primary" />
                      </div>

                      <div class="w-full xl:w-2/6">
                        <label class="mb-2.5 block text-black dark:text-white">
                          Estado de la carrera <span class="text-meta-1">*</span>
                        </label>
                        <div class="flex items-center space-x-2">
                          <div class="mr-3">
                            <label for="checkboxLabelFour" class="flex cursor-pointer select-none items-center">
                              <div class="relative">
                                <input type="radio" id="checkboxLabelFour" class="" name="estado_carrera" value="1"/>
                              </div>
                              Activo
                            </label>
                          </div>

                          <div >
                            <label for="checkboxLabelFour" class="flex cursor-pointer select-none items-center">
                              <div class="relative">
                                <input type="radio" id="checkboxLabelFour" class="" name="estado_carrera" value="0"/>
                              </div>
                              Inactivo
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>

                    <button class="flex w-full justify-center rounded bg-primary p-3 font-medium text-gray">
                      Guardar
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </main>
		</div>
		<!-- ===== Content Area End ===== -->
	</div>
	<!-- ===== Page Wrapper End ===== -->
	<?php $this->GetComplement('scripts');?>
</body>

</html>