<script setup>
import { ref } from 'vue';
import Header from '../../Components/Header.vue';
import Sidebar from '../../Components/Sidebar.vue';
import { Plus } from 'lucide-vue-next';

import { provide } from "vue";
import {
  ScheduleComponent as EjsSchedule, ViewsDirective as EViews, ViewDirective as EView,
  ResourcesDirective as EResources, ResourceDirective as EResource,
  Day, Week, WorkWeek, Month, Agenda
} from "@syncfusion/ej2-vue-schedule";

provide('schedule', [Day, Week, WorkWeek, Month, Agenda]);

const selectedDate = new Date(2024, 11, 4);
const eventSettings = {
  dataSource: [
    {
      Id: 1,
      Subject: 'Mathematics',
      EventType: 'Confirmed',
      StartTime: new Date(2024, 11, 4, 10, 0),
      EndTime: new Date(2024, 11, 4, 11, 0),
      OwnerId: 1
    },
  ]
};
const ownerDataSource = [
  { OwnerText: "Nancy", Id: 1, OwnerColor: "#FF0000" },
  { OwnerText: "Steven", Id: 2, OwnerColor: "#F87217" },
  { OwnerText: "Robert", Id: 3, OwnerColor: "#4B8DF8" },
  { OwnerText: "Smith", Id: 4, OwnerColor: "#1E90FF" },
  { OwnerText: "Andrew", Id: 5, OwnerColor: "#73C0DE" },
];
</script>

<template>

  <Head title=" | Calendar" />
  <div class="p-6 bg-gray-50 min-h-screen">
    <Header />
    <Sidebar />
    <div class="ml-64 pt-16">
      <main class="flex-1 p-6">
        <div class="max-w-7xl mx-auto">
          <div class="justify-between mb-6">
            <div class="flex flex-1 justify-between items-center gap-4 pb-4">
              <h1 class="text-2xl font-semibold pb-4">Calendar View</h1>
              <!-- <button class="btn btn-primary"><Plus class="mr-2 h-5 w-5 "/>Create Event</button> -->
            </div>
            <ejs-schedule height='550px' width='100%' :selectedDate='selectedDate' :eventSettings='eventSettings'>
              <e-views>
                <e-view option='Day'></e-view>
                <e-view option='Week'></e-view>
                <e-view option='WorkWeek'></e-view>
                <e-view option='Month'></e-view>
                <e-view option='Agenda'></e-view>
              </e-views>
              <e-resources>
                <e-resource field="OwnerId" title="Owner" name="Owners" :dataSource="ownerDataSource"
                  textField="OwnerText" idField="Id" colorField="OwnerColor">
                </e-resource>
              </e-resources>
            </ejs-schedule>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>